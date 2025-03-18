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

final class ExposeFewPublicMethodsAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;

    /** @var int */
    private int $maxPublicMethods;

    /**
     * @param array<string> $excludedPaths
     * @param int $maxPublicMethods
     */
    public function __construct(
        array $excludedPaths = [],
        int $maxPublicMethods = 5,
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->maxPublicMethods = $maxPublicMethods;
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

        if ($node->isAbstract()) {
            return true;
        }

        if ($node->isAnonymous()) {
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
        if (!$node instanceof Class_) {
            return [];
        }

        $methods = $node->getMethods();
        $publicMethodCount = 0;

        foreach ($methods as $method) {
            if ($method->isPublic()) {
                $publicMethodCount++;
            }
        }

        if ($publicMethodCount <= $this->maxPublicMethods) {
            return [];
        }

        if ($node->namespacedName !== null) {
            $className = $node->namespacedName->toString();
        } elseif ($node->name !== null) {
            $className = $node->name->toString();
        } else {
            $className = 'Unknown Class';
        }

        return RuleErrorFactory::createErrorWithTips(
            message: 'Class %s has %d public methods, which exceeds the maximum of %d (Elegant Object principle).',
            messageParameters: [$className, $publicMethodCount, $this->maxPublicMethods],
            tips: TipFactory::exposeFewPublicMethods()->tips(),
        )->errors();
    }
}
