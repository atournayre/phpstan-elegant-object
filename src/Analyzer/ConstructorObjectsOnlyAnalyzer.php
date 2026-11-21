<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\ParameterExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\TestClassTrait;
use PhpParser\Node;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

/**
 * Constructors accept objects only: collaborators and value objects.
 *
 * Primitive: string, int, float, bool, array, iterable, mixed (or no type), alone or as a union member.
 * Primitives belong to secondary constructors (named constructors), which build the objects.
 * Private constructors (named-constructor classes such as Email::of(string)) are ignored,
 * unless `checkPrivateConstructors` is true.
 * Test classes are ignored.
 *
 * Exemptions are chosen by the developer: excluded paths, allowed classes (wildcards), interfaces (wildcards),
 * parameter names and primitive types.
 */
final class ConstructorObjectsOnlyAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use ParameterExceptionTrait;
    use InterfaceExceptionTrait;
    use TestClassTrait;

    private const PRIMITIVES = ['string', 'int', 'float', 'bool', 'array', 'iterable', 'mixed'];

    /** @var array<string> */
    private array $allowedClasses;

    /** @var array<string> */
    private array $allowedTypes;

    private bool $checkPrivateConstructors;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedParameterNames
     * @param array<string> $allowedInterfaces
     * @param array<string> $allowedClasses
     * @param array<string> $allowedTypes
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedParameterNames = [],
        array $allowedInterfaces = [],
        array $allowedClasses = [],
        array $allowedTypes = [],
        bool $checkPrivateConstructors = false,
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedParameterNames = $allowedParameterNames;
        $this->allowedInterfaces = $allowedInterfaces;
        $this->allowedClasses = $allowedClasses;
        $this->allowedTypes = array_map('strtolower', $allowedTypes);
        $this->checkPrivateConstructors = $checkPrivateConstructors;
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof ClassMethod || $node->getParams() === []) {
            return true;
        }

        if ($node->name->toLowerString() !== '__construct') {
            return true;
        }

        if (!$this->checkPrivateConstructors && $node->isPrivate()) {
            return true;
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null || $this->isTestClass($classReflection)) {
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

        $primitives = [];
        foreach ($node->getParams() as $param) {
            $parameterName = $this->parameterName($param);
            if ($this->isAllowedParameterName($parameterName) || !$this->isPrimitive($param->type)) {
                continue;
            }

            $primitives[] = '$' . $parameterName;
        }

        if ($primitives === []) {
            return [];
        }

        return RuleErrorFactory::createErrorWithTips(
            'Constructor of %s accepts non-object parameters (%s). '
            . 'Pass objects, and build them in a secondary constructor.',
            'elegantObject.constructor.objectsOnly',
            [
                $classReflection->getName(),
                implode(', ', $primitives),
            ],
        )->errors();
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

    private function parameterName(Param $param): string
    {
        if ($param->var instanceof Variable && is_string($param->var->name)) {
            return $param->var->name;
        }

        return 'unknown';
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
