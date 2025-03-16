<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Traits\ClassExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

final class NeverUsePublicConstantsAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use ClassExceptionTrait;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedClasses
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedClasses = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedClasses = $allowedClasses;
    }

    public function getNodeType(): string
    {
        return ClassConst::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof ClassConst) {
            return true;
        }

        if ($this->isExcludedPath($scope->getFile())) {
            return true;
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return true;
        }

        if ($this->isAllowedClassName($classReflection->getName())) {
            return true;
        }

        return false;
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof ClassConst) {
            return [];
        }

        if (!$node->isPublic()) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (null === $classReflection) {
            return [];
        }

        $className = $classReflection->getName();

        return RuleErrorFactory::createErrorWithTips(
            message: 'Class %s has a public constant %s, which violates encapsulation (Elegant Object principle).',
            messageParameters: [$className, $node->consts[0]->name->toString()],
            tips: TipFactory::neverUsePublicConstants()->tips(),
        )->errors();
    }
}
