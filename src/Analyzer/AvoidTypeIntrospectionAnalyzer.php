<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\MethodExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\TestClassTrait;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

/**
 * Methods must not introspect types nor cast values (Elegant Objects, section 3.7).
 *
 * Flagged: instanceof, type functions (get_class(), is_string(), ...), casts and comparisons of ::class.
 * Test classes are ignored.
 *
 * Exemptions are chosen by the developer: excluded paths, allowed method names,
 * allowed interfaces (wildcards) and allowed classes (wildcards).
 */
final class AvoidTypeIntrospectionAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use MethodExceptionTrait;
    use InterfaceExceptionTrait;
    use TestClassTrait;

    private const FUNCTIONS = [
        'get_class', 'get_debug_type', 'gettype', 'is_a', 'is_subclass_of', 'is_int', 'is_string', 'is_float',
        'is_bool', 'is_array', 'is_object', 'is_numeric', 'is_callable', 'is_iterable',
    ];

    /** @var array<string> */
    private array $allowedClasses;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedInterfaces
     * @param array<string> $allowedClasses
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedMethodNames = [],
        array $allowedInterfaces = [],
        array $allowedClasses = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedMethodNames = $allowedMethodNames;
        $this->allowedInterfaces = $allowedInterfaces;
        $this->allowedClasses = $allowedClasses;
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof ClassMethod || $node->stmts === null) {
            return true;
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null || $this->isAllowedMethodName($node->name->toString())) {
            return true;
        }

        if ($this->isTestClass($classReflection) || $this->isExcluded($scope->getFile())) {
            return true;
        }

        if ($this->isAllowedClass($classReflection->getName())) {
            return true;
        }

        return $this->hasAllowedInterface($classReflection);
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (!$node instanceof ClassMethod || $classReflection === null) {
            return [];
        }

        $usages = [];
        foreach ((new NodeFinder())->findInstanceOf($node->stmts ?? [], Node::class) as $found) {
            $usage = $this->describe($found);
            if ($usage !== null) {
                $usages[] = $usage;
            }
        }

        if ($usages === []) {
            return [];
        }

        return RuleErrorFactory::createErrorWithTips(
            'Method %s::%s() uses type introspection or casting (%s). Rely on polymorphism instead.',
            'elegantObject.typeIntrospection.used',
            [
                $classReflection->getName(),
                $node->name->toString(),
                implode(', ', array_values(array_unique($usages))),
            ],
        )->errors();
    }

    private function describe(Node $node): ?string
    {
        if ($node instanceof Instanceof_) {
            return 'instanceof';
        }

        if ($node instanceof FuncCall && $node->name instanceof Name) {
            $function = strtolower($node->name->getLast());

            return in_array($function, self::FUNCTIONS, true) ? $function . '()' : null;
        }

        if ($node instanceof Cast) {
            return $this->castName($node) . ' cast';
        }

        if (
            ($node instanceof Identical || $node instanceof NotIdentical || $node instanceof Equal || $node instanceof NotEqual)
            && ($this->isClassConstant($node->left) || $this->isClassConstant($node->right))
        ) {
            return '::class comparison';
        }

        return null;
    }

    private function castName(Cast $cast): string
    {
        return match (true) {
            $cast instanceof Cast\Int_ => '(int)',
            $cast instanceof Cast\String_ => '(string)',
            $cast instanceof Cast\Double => '(float)',
            $cast instanceof Cast\Bool_ => '(bool)',
            $cast instanceof Cast\Array_ => '(array)',
            $cast instanceof Cast\Object_ => '(object)',
            default => '(unset)',
        };
    }

    private function isClassConstant(Node $node): bool
    {
        return $node instanceof ClassConstFetch
            && $node->name instanceof Identifier
            && $node->name->toLowerString() === 'class';
    }

    private function isExcluded(string $file): bool
    {
        if ($this->isExcludedPath($file)) {
            return true;
        }

        foreach ($this->excludedPaths as $excludedPath) {
            if ($excludedPath !== '' && str_contains($file, $excludedPath)) {
                return true;
            }
        }

        return false;
    }

    private function isAllowedClass(string $className): bool
    {
        foreach ($this->allowedClasses as $allowed) {
            $pattern = '/^' . str_replace('\*', '.*', preg_quote($allowed, '/')) . '$/';
            if (preg_match($pattern, $className) === 1) {
                return true;
            }
        }

        return false;
    }
}
