<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\MethodExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use PhpParser\Node;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\ShouldNotHappenException;

/**
 * Public methods return no primitive: they return value objects. Very strict, opt-in rule.
 *
 * Primitive: string, int, float, bool, array, iterable, mixed (or no return type), alone or as a union member.
 * void, never, self, static and object types are not primitive; bool is flagged unless listed in allowedTypes.
 * Constructors and magic methods other than __invoke are ignored.
 * A method whose signature is imposed by a parent class or an interface is ignored: the contract is what must change.
 *
 * Exemptions are chosen by the developer: excluded paths, allowed classes (wildcards), interfaces (wildcards),
 * method names and primitive types.
 */
final class NoPrimitiveReturnAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use MethodExceptionTrait;
    use InterfaceExceptionTrait;

    private const PRIMITIVES = ['string', 'int', 'float', 'bool', 'array', 'iterable', 'mixed'];

    /** @var array<string> */
    private array $allowedClasses;

    /** @var array<string> */
    private array $allowedTypes;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedInterfaces
     * @param array<string> $allowedClasses
     * @param array<string> $allowedTypes
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedMethodNames = [],
        array $allowedInterfaces = [],
        array $allowedClasses = [],
        array $allowedTypes = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedMethodNames = $allowedMethodNames;
        $this->allowedInterfaces = $allowedInterfaces;
        $this->allowedClasses = $allowedClasses;
        $this->allowedTypes = array_map('strtolower', $allowedTypes);
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof ClassMethod || !$node->isPublic()) {
            return true;
        }

        $name = $node->name->toString();
        if ($name === '__construct' || ($name !== '__invoke' && str_starts_with($name, '__'))) {
            return true;
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null || $this->isAllowedMethodName($name)) {
            return true;
        }

        if ($this->isExcluded($scope->getFile()) || $this->isAllowedClass($classReflection->getName())) {
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

        if (!$this->isPrimitive($node->returnType)
            || $this->isImposedByContract($classReflection, $node->name->toString())
        ) {
            return [];
        }

        return RuleErrorFactory::createErrorWithTips(
            'Method %s::%s() returns a primitive type. Return a value object instead.',
            'elegantObject.returns.primitive',
            [
                $classReflection->getName(),
                $node->name->toString(),
            ],
        )->errors();
    }

    private function isImposedByContract(ClassReflection $classReflection, string $method): bool
    {
        foreach ($classReflection->getAncestors() as $ancestor) {
            if ($ancestor->getName() !== $classReflection->getName() && $ancestor->hasNativeMethod($method)) {
                return true;
            }
        }

        return false;
    }

    private function isPrimitive(Identifier|Name|ComplexType|null $type): bool
    {
        if ($type === null) {
            return !in_array('mixed', $this->allowedTypes, true);
        }

        if ($type instanceof NullableType) {
            return $this->isPrimitive($type->type);
        }

        if ($type instanceof UnionType) {
            foreach ($type->types as $inner) {
                if ($this->isPrimitive($inner)) {
                    return true;
                }
            }

            return false;
        }

        if (!$type instanceof Identifier) {
            return false;
        }

        $typeName = $type->toLowerString();

        return in_array($typeName, self::PRIMITIVES, true) && !in_array($typeName, $this->allowedTypes, true);
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
}
