<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\MethodExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\TestClassTrait;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

/**
 * A catch block only exists to chain and rethrow (Elegant Objects vol.1, 4.2.1, 4.2.2 and 4.2.5).
 *
 * Every catch body must contain either a bare `throw $e` or a `throw new X(...)` receiving the caught
 * variable (positional or named argument, e.g. `previous: $e`). A catch without variable can never chain.
 * Overlaps with NoEmptyCatchRule for empty bodies; the error identifier differs.
 *
 * Exemptions are chosen by the developer: excluded paths, allowed exceptions (wildcards, catch types),
 * method names, classes (wildcards) and interfaces (wildcards). Test classes are skipped.
 */
final class CatchMustRethrowAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use MethodExceptionTrait;
    use InterfaceExceptionTrait;
    use TestClassTrait;

    /** @var array<string> */
    private array $allowedExceptions;

    /** @var array<string> */
    private array $allowedClasses;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedExceptions
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedClasses
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedExceptions = [],
        array $allowedMethodNames = [],
        array $allowedClasses = [],
        array $allowedInterfaces = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedExceptions = $allowedExceptions;
        $this->allowedMethodNames = $allowedMethodNames;
        $this->allowedClasses = $allowedClasses;
        $this->allowedInterfaces = $allowedInterfaces;
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

        $functionName = $scope->getFunctionName();
        if ($functionName !== null && $this->isAllowedMethodName($functionName)) {
            return true;
        }

        foreach ($node->types as $type) {
            if ($this->matchesAny(ltrim($type->toString(), '\\'), $this->allowedExceptions)) {
                return true;
            }
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return false;
        }

        return $this->isTestClass($classReflection)
            || $this->matchesAny($classReflection->getName(), $this->allowedClasses)
            || $this->hasAllowedInterface($classReflection);
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof Catch_ || $this->rethrows($node)) {
            return [];
        }

        $types = array_map(static fn (Node\Name $type): string => ltrim($type->toString(), '\\'), $node->types);

        return RuleErrorFactory::createErrorWithTips(
            'Catch of %s must chain the caught exception and rethrow it.',
            'elegantObject.errorHandling.catchWithoutRethrow',
            [implode('|', $types)],
        )->errors();
    }

    private function rethrows(Catch_ $catch): bool
    {
        $caught = $catch->var?->name;
        if (!is_string($caught)) {
            return false;
        }

        foreach ((new NodeFinder())->findInstanceOf($catch->stmts, Throw_::class) as $throw) {
            if ($this->isVariable($throw->expr, $caught) || $this->chains($throw->expr, $caught)) {
                return true;
            }
        }

        return false;
    }

    private function chains(Node\Expr $expr, string $caught): bool
    {
        if (!$expr instanceof New_) {
            return false;
        }

        foreach ($expr->args as $arg) {
            if ($arg instanceof Node\Arg && $this->isVariable($arg->value, $caught)) {
                return true;
            }
        }

        return false;
    }

    private function isVariable(Node\Expr $expr, string $name): bool
    {
        return $expr instanceof Variable && $expr->name === $name;
    }

    /**
     * @param array<string> $patterns
     */
    private function matchesAny(string $subject, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            $regex = '/^' . str_replace('\*', '.*', preg_quote(ltrim($pattern, '\\'), '/')) . '$/';
            if (preg_match($regex, $subject) === 1) {
                return true;
            }
        }

        return false;
    }
}
