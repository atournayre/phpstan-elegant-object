<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\MethodExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\ShouldNotHappenException;

final class NeverReturnNullAnalyzer extends RuleAnalyzer
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
    )
    {
        $this->excludedPaths = $excludedPaths;
        $this->allowedMethodNames = $allowedMethodNames;
        $this->allowedInterfaces = $allowedInterfaces;
    }

    public function getNodeType(): string
    {
        return Return_::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof Return_) {
            return true;
        }

        if ($node->expr === null) {
            return true;
        }

        if (!$scope->isInClass()) {
            return true;
        }

        $methodReflection = $scope->getFunction();
        if (!($methodReflection instanceof MethodReflection)) {
            return true;
        }

        return false;
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof Return_ || $node->expr === null) {
            return [];
        }

        // Check if return expression is null
        if ($this->isNullReturn($node->expr)) {
            $classReflection = $scope->getClassReflection();
            $methodReflection = $scope->getFunction();

            if ($classReflection === null ||
                !($methodReflection instanceof MethodReflection)
            ) {
                return [];
            }

            // Add these checks here as well
            $methodName = $methodReflection->getName();
            if ($this->isAllowedMethodName($methodName)) {
                return [];
            }

            if ($this->isExcludedPath($scope->getFile())) {
                return [];
            }

            if ($this->isMethodFromAllowedInterface($classReflection, $methodName)) {
                return [];
            }

            $className = $classReflection->getName();

            return RuleErrorFactory::createErrorWithTips(
                message: 'Method %s::%s() returns null, which violates object encapsulation (Elegant Object principle).',
                identifier: 'elegantObject.returnValue.nullReturned',
                messageParameters: [$className, $methodName],
                tips: TipFactory::neverReturnNull()->tips(),
            )->errors();
        }

        return [];
    }

    private function isNullReturn(Node $expr): bool
    {
        // Check direct null return
        if ($expr instanceof ConstFetch && $expr->name->toString() === 'null') {
            return true;
        }

        // Check ternary expression
        if ($expr instanceof Ternary) {
            // Check 'if' part if not null
            if ($expr->if !== null && $this->isNullReturn($expr->if)) {
                return true;
            }
            // Check 'else' part
            if ($this->isNullReturn($expr->else)) {
                return true;
            }
        }

        return false;
    }
}
