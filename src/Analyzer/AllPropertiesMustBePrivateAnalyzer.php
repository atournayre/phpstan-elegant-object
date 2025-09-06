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

final class AllPropertiesMustBePrivateAnalyzer extends RuleAnalyzer
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

        if ($node->isPrivate()) {
            return true;
        }

        $propertyName = $node->props[0]->name->toString();

        if ($this->isAllowedPropertyName($propertyName)) {
            return true;
        }

        if ($this->isExcludedPath($scope->getFile())) {
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

        $propertyName = $node->props[0]->name->toString();
        $classReflection = $scope->getClassReflection();

        if (null === $classReflection) {
            return [];
        }

        $className = $classReflection->getName();

        return RuleErrorFactory::createErrorWithTips(
            'Property %s::$%s is not private, which violates object encapsulation (Elegant Object principle).',
            'elegantObject.properties.notPrivate',
            [$className, $propertyName],
            TipFactory::allPropertiesMustBePrivate()->tips(),
        )->errors();
    }
}
