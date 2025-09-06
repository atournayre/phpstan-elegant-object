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

final class AlwaysUseInterfaceAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;

    /** @var array<string> */
    private array $excludedClasses;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $excludedClasses
     */
    public function __construct(
        array $excludedPaths = [],
        array $excludedClasses = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->excludedClasses = $excludedClasses;
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

        $className = $node->namespacedName?->toString()
            ?? ($node->name !== null ? $node->name->toString() : '');

        if ($className === '') {
            return true;
        }

        if (in_array($className, $this->excludedClasses, true)) {
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

        if (empty($node->implements)) {
            return RuleErrorFactory::createErrorWithTips(
                message: 'Class %s does not implement any interfaces, which violates the "always use interface" principle (Elegant Object principle).',
                identifier: 'elegantObject.interfaces.noInterfaceImplemented',
                messageParameters: [$className],
                tips: TipFactory::alwaysUseInterface()->tips(),
            )->errors();
        }

        return [];
    }
}
