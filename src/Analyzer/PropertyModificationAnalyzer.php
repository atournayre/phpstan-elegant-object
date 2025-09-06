<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\MethodExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PropertyExceptionTrait;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

final class PropertyModificationAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use MethodExceptionTrait;
    use PropertyExceptionTrait;
    use InterfaceExceptionTrait;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedPropertiesNames
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedMethodNames = [],
        array $allowedPropertiesNames = [],
        array $allowedInterfaces = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedMethodNames = $allowedMethodNames;
        $this->allowedPropertiesNames = $allowedPropertiesNames;
        $this->allowedInterfaces = $allowedInterfaces;
    }

    public function getNodeType(): string
    {
        return Expression::class;
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof Expression) {
            return true;
        }

        if (!$node->expr instanceof Assign) {
            return true;
        }

        $assign = $node->expr;

        if (!$assign->var instanceof PropertyFetch) {
            return true;
        }

        $propertyFetch = $assign->var;

        // Only check $this-> property assignments
        if (!$propertyFetch->var instanceof Variable || $propertyFetch->var->name !== 'this') {
            return true;
        }

        // Skip if we're in the constructor
        $methodReflection = $scope->getFunction();
        if ($methodReflection !== null && $methodReflection->getName() === '__construct') {
            return true;
        }

        // Skip if the method is allowed
        if ($methodReflection !== null && $this->isAllowedMethodName($methodReflection->getName())) {
            return true;
        }

        // Skip if the property is allowed
        if ($propertyFetch->name instanceof Node\Identifier && $this->isAllowedPropertyName($propertyFetch->name->toString())) {
            return true;
        }

        if ($this->isExcludedPath($scope->getFile())) {
            return true;
        }

        // Skip if method is from an allowed interface
        if ($methodReflection !== null
            && $this->isMethodFromAllowedInterface($scope->getClassReflection(), $methodReflection->getName())) {
            return true;
        }

        return false;
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof Expression || !$node->expr instanceof Assign) {
            return [];
        }

        $assign = $node->expr;
        if (!$assign->var instanceof PropertyFetch) {
            return [];
        }

        $methodReflection = $scope->getFunction();
        $classReflection = $scope->getClassReflection();

        if ($methodReflection === null || $classReflection === null) {
            return [];
        }

        $className = $classReflection->getName();
        $methodName = $methodReflection->getName();

        return RuleErrorFactory::createErrorWithTips(
            message: 'Method %s::%s() modifies an object property, which violates object immutability (Elegant Object principle).',
            identifier: 'elegantObject.properties.propertyModified',
            messageParameters: [$className, $methodName],
            tips: TipFactory::beImmutable()->tips(),
        )->errors();
    }
}
