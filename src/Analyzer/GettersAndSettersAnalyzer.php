<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Pattern\Pattern;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\MethodExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

final class GettersAndSettersAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use MethodExceptionTrait;
    use InterfaceExceptionTrait;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedMethodNames = [],
        array $allowedInterfaces = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedMethodNames = $allowedMethodNames;
        $this->allowedInterfaces = $allowedInterfaces;
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof ClassMethod) {
            return true;
        }

        $methodName = $node->name->toString();

        if ($this->isAllowedMethodName($methodName)) {
            return true;
        }

        if ($this->isExcludedPath($scope->getFile())) {
            return true;
        }

        if ($this->isMethodFromAllowedInterface($scope->getClassReflection(), $methodName)) {
            return true;
        }

        return false;
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof ClassMethod) {
            return [];
        }

        $methodName = $node->name->toString();
        $classReflection = $scope->getClassReflection();

        if (null === $classReflection) {
            return [];
        }

        $className = $classReflection->getName();

        if (Pattern::getter()->match($methodName)) {
            return RuleErrorFactory::createErrorWithTips(
                message: 'Method %s::%s() appears to be a getter, which violates object encapsulation (Elegant Object principle).',
                messageParameters: [$className, $methodName],
                tips: TipFactory::gettersAndSetters()->tips(),
            )->errors();
        }

        if (Pattern::setter()->match($methodName)) {
            return RuleErrorFactory::createErrorWithTips(
                message: 'Method %s::%s() appears to be a setter, which violates object encapsulation (Elegant Object principle).',
                messageParameters: [$className, $methodName],
                tips: TipFactory::gettersAndSetters()->tips(),
            )->errors();
        }

        return [];
    }
}
