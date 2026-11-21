<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\ClassExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\MethodExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\TestClassTrait;
use PhpParser\Node;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

/**
 * A catch block never swallows an exception: it uses the caught exception or throws.
 *
 * Flagged: an empty catch body, or a body that neither references the caught variable nor contains a throw.
 * `catch (Foo)` without variable is fine as long as the body throws.
 *
 * Exemptions: excluded paths, allowed exceptions (wildcards, every caught type must match),
 * allowed method names, allowed classes. Test classes are skipped.
 */
final class NoEmptyCatchAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use MethodExceptionTrait;
    use ClassExceptionTrait;
    use TestClassTrait;

    /** @var array<string> */
    private array $allowedExceptions;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedExceptions
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedClasses
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedExceptions = [],
        array $allowedMethodNames = [],
        array $allowedClasses = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedExceptions = $allowedExceptions;
        $this->allowedMethodNames = $allowedMethodNames;
        $this->allowedClasses = $allowedClasses;
    }

    public function getNodeType(): string
    {
        return Catch_::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof Catch_ || $this->isExcludedPath($scope->getFile())) {
            return true;
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection !== null
            && ($this->isTestClass($classReflection) || $this->isAllowedClassName($classReflection->getName()))
        ) {
            return true;
        }

        $function = $scope->getFunction();
        if ($function !== null && $this->isAllowedMethodName($function->getName())) {
            return true;
        }

        return $this->catchesOnlyAllowedExceptions($node);
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof Catch_ || $this->handles($node)) {
            return [];
        }

        $types = array_map(static fn (Node\Name $name): string => $name->toString(), $node->types);

        return RuleErrorFactory::createErrorWithTips(
            'Catch block for %s swallows the exception. Use it, log it or rethrow it.',
            'elegantObject.errorHandling.emptyCatch',
            [implode('|', $types)],
        )->errors();
    }

    private function handles(Catch_ $catch): bool
    {
        $finder = new NodeFinder();

        if ($finder->findFirstInstanceOf($catch->stmts, Throw_::class) !== null) {
            return true;
        }

        $variable = $catch->var;
        if (!$variable instanceof Variable || !is_string($variable->name)) {
            return false;
        }

        $name = $variable->name;

        return $finder->findFirst(
            $catch->stmts,
            static fn (Node $inner): bool => $inner instanceof Variable && $inner->name === $name,
        ) !== null;
    }

    private function catchesOnlyAllowedExceptions(Catch_ $catch): bool
    {
        foreach ($catch->types as $type) {
            if (!$this->isAllowedException($type->toString())) {
                return false;
            }
        }

        return true;
    }

    private function isAllowedException(string $className): bool
    {
        foreach ($this->allowedExceptions as $allowed) {
            $pattern = '/^' . str_replace('\*', '.*', preg_quote(ltrim($allowed, '\\'), '/')) . '$/';
            if (preg_match($pattern, $className) === 1) {
                return true;
            }
        }

        return false;
    }
}
