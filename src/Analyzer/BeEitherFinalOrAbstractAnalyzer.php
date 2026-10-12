<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

final class BeEitherFinalOrAbstractAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;

    /**
     * @param array<string> $excludedPaths
     */
    public function __construct(
        array $excludedPaths = [],
    ) {
        $this->excludedPaths = $excludedPaths;
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof Class_) {
            return true;
        }

        if ($this->isExcludedPath($scope->getFile())) {
            return true;
        }

        if ($node->isAbstract()) {
            return true;
        }

        if ($node->isFinal()) {
            return true;
        }

        return false;
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof Class_) {
            return [];
        }

        $className = $node->namespacedName?->toString()
            ?? ($node->name !== null ? $node->name->toString() : '');

        if ($className === '') {
            return [];
        }

        return RuleErrorFactory::createErrorWithTips(
            message: 'Class %s is not final neither abstract, which violates the "be either final or abstract" principle (Elegant Object principle).',
            identifier: 'elegantObject.class.notFinalOrAbstract',
            messageParameters: [$className],
            tips: TipFactory::beEitherFinalOrAbstract()->tips(),
        )->errors();
    }
}
