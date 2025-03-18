<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use PhpParser\Node;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

final class KeepInterfacesShortAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;

    /** @var int */
    private int $maxMethods;

    /**
     * @param array<string> $excludedPaths
     * @param int $maxMethods
     */
    public function __construct(
        array $excludedPaths = [],
        int $maxMethods = 5,
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->maxMethods = $maxMethods;
    }

    public function getNodeType(): string
    {
        return Interface_::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof Interface_) {
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
        if (!$node instanceof Interface_) {
            return [];
        }

        $methods = $node->getMethods();
        $methodCount = count($methods);

        if ($methodCount <= $this->maxMethods) {
            return [];
        }

        if ($node->namespacedName !== null) {
            $interfaceName = $node->namespacedName->toString();
        } elseif ($node->name !== null) {
            $interfaceName = $node->name->toString();
        } else {
            $interfaceName = 'Unknown Interface';
        }

        return RuleErrorFactory::createErrorWithTips(
            message: 'Interface %s has %d methods, which exceeds the maximum of %d (Elegant Object principle).',
            messageParameters: [$interfaceName, $methodCount, $this->maxMethods],
            tips: TipFactory::keepInterfacesShort()->tips(),
        )->errors();
    }
}
