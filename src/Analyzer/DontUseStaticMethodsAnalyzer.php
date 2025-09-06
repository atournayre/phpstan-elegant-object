<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\MethodExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\TestClassTrait;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

final class DontUseStaticMethodsAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use MethodExceptionTrait;
    use InterfaceExceptionTrait;
    use TestClassTrait;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedMethodNames = [],
        array $allowedInterfaces = []
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

        if (!$node->isStatic()) {
            return true;
        }

        $methodName = $node->name->toString();

        if ($this->isAllowedMethodName($methodName)) {
            return true;
        }

        if ($this->isExcludedPath($scope->getFile())) {
            return true;
        }

        $classReflection = $scope->getClassReflection();
        if (null === $classReflection) {
            return true;
        }

        if ($this->isTestClass($classReflection)) {
            return true;
        }

        if ($this->isMethodFromAllowedInterface($classReflection, $methodName)) {
            return true;
        }

        $returnType = $node->getReturnType();
        if ($returnType !== null) {
            $validReturnTypes = ['self', 'static', $classReflection->getName()];
            if ($returnType instanceof Node\Name && in_array($returnType->toString(), $validReturnTypes, true)) {
                return true;
            }
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

        return RuleErrorFactory::createErrorWithTips(
            'Method %s::%s() is static, which violates object encapsulation (Elegant Object principle). Only secondary constructors (factory methods) can be static.',
            'elegantObject.staticMethods.staticMethodFound',
            [$className, $methodName],
            TipFactory::staticMethods()->tips(),
        )->errors();
    }
}
