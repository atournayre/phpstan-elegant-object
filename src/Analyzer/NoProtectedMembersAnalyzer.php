<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Traits\ClassExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\MethodExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PropertyExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\TestClassTrait;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Classes declare no protected method, property or constant: protected is an inheritance hook.
 *
 * - Final classes: always flagged, protected is meaningless there.
 * - Abstract classes: flagged too, except protected abstract methods (template method hooks)
 *   or when `allowInAbstractClasses` is enabled (default: false).
 * - Members overriding a parent member are ignored: the parent declaration is what must change.
 * - Test classes are ignored.
 */
final class NoProtectedMembersAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use MethodExceptionTrait;
    use PropertyExceptionTrait;
    use InterfaceExceptionTrait;
    use ClassExceptionTrait;
    use TestClassTrait;

    private bool $allowInAbstractClasses;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedPropertyNames
     * @param array<string> $allowedInterfaces
     * @param array<string> $allowedClasses
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedMethodNames = [],
        array $allowedPropertyNames = [],
        array $allowedInterfaces = [],
        array $allowedClasses = [],
        bool $allowInAbstractClasses = false,
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedMethodNames = $allowedMethodNames;
        $this->allowedPropertiesNames = $allowedPropertyNames;
        $this->allowedInterfaces = $allowedInterfaces;
        $this->allowedClasses = $allowedClasses;
        $this->allowInAbstractClasses = $allowInAbstractClasses;
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        // @phpstan-ignore phpstanApi.instanceofAssumption
        if (!$node instanceof InClassNode || !$node->getOriginalNode() instanceof Class_) {
            return true;
        }

        $classReflection = $node->getClassReflection();
        if ($this->isExcludedPath($scope->getFile()) || $this->isTestClass($classReflection)) {
            return true;
        }

        if ($this->isAllowedClassName($classReflection->getName()) || $this->hasAllowedInterface($classReflection)) {
            return true;
        }

        return !$classReflection->isFinal() && $this->allowInAbstractClasses;
    }

    public function analyze(Node $node, Scope $scope): array
    {
        // @phpstan-ignore phpstanApi.instanceofAssumption
        if (!$node instanceof InClassNode) {
            return [];
        }

        $classReflection = $node->getClassReflection();
        $errors = [];

        foreach ($node->getOriginalNode()->stmts as $statement) {
            foreach ($this->violations($statement, $classReflection) as [$kind, $name, $line]) {
                $errors[] = RuleErrorBuilder::message(sprintf(
                    'Class %s declares protected %s %s. Protected is an inheritance hook: make it private.',
                    $classReflection->getName(),
                    $kind,
                    $name,
                ))
                    ->identifier('elegantObject.visibility.protectedMember')
                    ->line($line)
                    ->tip('Prefer composition over inheritance: expose behavior through public interfaces.')
                    ->build();
            }
        }

        return $errors;
    }

    /**
     * @return list<array{string, string, int}>
     */
    private function violations(Node $statement, ClassReflection $classReflection): array
    {
        if ($statement instanceof ClassMethod) {
            return $this->methodViolations($statement, $classReflection);
        }

        $violations = [];

        if ($statement instanceof Property && $statement->isProtected()) {
            foreach ($statement->props as $property) {
                $violations[] = $this->propertyViolation(
                    $property->name->toString(),
                    $property->getStartLine(),
                    $classReflection,
                );
            }
        }

        if ($statement instanceof ClassConst && $statement->isProtected()) {
            foreach ($statement->consts as $constant) {
                $name = $constant->name->toString();
                if (!$this->isInherited($classReflection, static fn (ClassReflection $a): bool => $a->hasConstant($name))) {
                    $violations[] = ['constant', $name, $constant->getStartLine()];
                }
            }
        }

        return array_values(array_filter($violations));
    }

    /**
     * @return list<array{string, string, int}>
     */
    private function methodViolations(ClassMethod $method, ClassReflection $classReflection): array
    {
        $violations = [];
        $name = $method->name->toString();

        if ($this->isProtectedMethodFlagged($method, $classReflection)) {
            $violations[] = ['method', $name . '()', $method->getStartLine()];
        }

        if ($name === '__construct') {
            foreach ($method->params as $param) {
                if (($param->flags & Modifiers::PROTECTED) !== 0 && $param->var instanceof Variable
                    && is_string($param->var->name)) {
                    $violations[] = $this->propertyViolation($param->var->name, $param->getStartLine(), $classReflection);
                }
            }
        }

        return array_values(array_filter($violations));
    }

    /**
     * @return array{string, string, int}|null
     */
    private function propertyViolation(string $name, int $line, ClassReflection $classReflection): ?array
    {
        if ($this->isAllowedPropertyName($name)
            || $this->isInherited($classReflection, static fn (ClassReflection $a): bool => $a->hasNativeProperty($name))) {
            return null;
        }

        return ['property', '$' . $name, $line];
    }

    private function isProtectedMethodFlagged(ClassMethod $method, ClassReflection $classReflection): bool
    {
        if (!$method->isProtected()) {
            return false;
        }

        $name = $method->name->toString();
        if ($this->isAllowedMethodName($name) || $method->isAbstract()) {
            return false;
        }

        return !$this->isInherited($classReflection, static fn (ClassReflection $a): bool => $a->hasNativeMethod($name));
    }

    /**
     * @param callable(ClassReflection): bool $declares
     */
    private function isInherited(ClassReflection $classReflection, callable $declares): bool
    {
        foreach ($classReflection->getAncestors() as $ancestor) {
            if ($ancestor->getName() !== $classReflection->getName() && $declares($ancestor)) {
                return true;
            }
        }

        return false;
    }
}
