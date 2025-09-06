<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PropertyExceptionTrait;
use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

final class PropertyMutabilityAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use PropertyExceptionTrait;
    use InterfaceExceptionTrait;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedPropertiesNames
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedPropertiesNames = [],
        array $allowedInterfaces = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedPropertiesNames = $allowedPropertiesNames;
        $this->allowedInterfaces = $allowedInterfaces;
    }

    public function getNodeType(): string
    {
        return Property::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof Property) {
            return true;
        }

        if ($node->isReadonly()) {
            return true;
        }

        if ($node->isStatic()) {
            return true; // Static properties are a separate concern
        }

        if ($this->isExcludedPath($scope->getFile())) {
            return true;
        }

        // Get property name
        $propertyName = $node->props[0]->name->toString();

        if ($this->isAllowedPropertyName($propertyName)) {
            return true;
        }

        // Add this check for interfaces
        $classReflection = $scope->getClassReflection();
        if ($classReflection !== null && $this->hasAllowedInterface($classReflection)) {
            return true;
        }

        return false;
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof Property) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        $className = $classReflection->getName();
        $propertyName = $node->props[0]->name->toString();

        return RuleErrorFactory::createErrorWithTips(
            message: 'Property %s::$%s is mutable, which violates object immutability (Elegant Object principle).',
            identifier: 'elegantObject.properties.mutableProperty',
            messageParameters: [$className, $propertyName],
            tips: TipFactory::beImmutable()->tips(),
        )->errors();
    }
}
