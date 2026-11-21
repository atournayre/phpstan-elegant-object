<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
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
use PHPStan\Reflection\ClassReflection;
use PHPStan\ShouldNotHappenException;

/**
 * A public method of a service accepts no primitive: it receives value objects.
 *
 * Primitive: string, int, float, bool, array, iterable, mixed (or no type), alone or as a union member.
 * Service: class whose short name ends with "Service" and that is not a Doctrine entity.
 * Constructors, magic methods other than __invoke and methods imposed by a vendor/internal contract are ignored.
 */
final class NoPrimitiveParameterInServiceAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;

    private const PRIMITIVES = ['string', 'int', 'float', 'bool', 'array', 'iterable', 'mixed'];
    private const ENTITY_ATTRIBUTE = 'Doctrine\ORM\Mapping\Entity';

    /**
     * @param array<string> $excludedPaths
     */
    public function __construct(array $excludedPaths = [])
    {
        $this->excludedPaths = $excludedPaths;
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
        if ($classReflection === null || !$this->isService($classReflection)) {
            return true;
        }

        return $this->isExcluded($scope->getFile());
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
            if ($this->isPrimitive($param->type)) {
                $primitives[] = '$' . $this->parameterName($param);
            }
        }

        if ($primitives === [] || $this->isImposedByExternalContract($classReflection, $node->name->toString())) {
            return [];
        }

        return RuleErrorFactory::createErrorWithTips(
            'Method %s::%s() of a service accepts primitive parameters (%s). Wrap the values in value objects.',
            'elegantObject.services.primitiveParameter',
            [
                $classReflection->getName(),
                $node->name->toString(),
                implode(', ', $primitives),
            ],
        )->errors();
    }

    private function isService(ClassReflection $classReflection): bool
    {
        if (!str_ends_with($classReflection->getName(), 'Service')) {
            return false;
        }

        foreach ($classReflection->getNativeReflection()->getAttributes() as $attribute) {
            if (ltrim($attribute->getName(), '\\') === self::ENTITY_ATTRIBUTE) {
                return false;
            }
        }

        return true;
    }

    private function isImposedByExternalContract(ClassReflection $classReflection, string $method): bool
    {
        foreach ($classReflection->getAncestors() as $ancestor) {
            if ($ancestor->getName() === $classReflection->getName() || !$ancestor->hasNativeMethod($method)) {
                continue;
            }

            $file = $ancestor->getFileName();
            if ($file === null || str_contains($file, '/vendor/')) {
                return true;
            }
        }

        return false;
    }

    private function isPrimitive(Identifier|Name|ComplexType|null $type): bool
    {
        if ($type === null) {
            return true;
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

        return $type instanceof Identifier && in_array($type->toLowerString(), self::PRIMITIVES, true);
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
