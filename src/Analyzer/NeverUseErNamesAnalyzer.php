<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Pattern\Pattern;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

final class NeverUseErNamesAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;

    /** @var array<string> */
    private array $excludedSuffixes;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $excludedSuffixes
     */
    public function __construct(
        array $excludedPaths = [],
        array $excludedSuffixes = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->excludedSuffixes = $excludedSuffixes;
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
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

        if (!$node->name) {
            return [];
        }

        $shortName = $node->name->toString();
        $className = $scope->getNamespace() ? $scope->getNamespace() . '\\' . $shortName : $shortName;


        foreach ($this->excludedSuffixes as $excludedSuffix) {
            if (str_ends_with($shortName, $excludedSuffix)) {
                return [];
            }
        }

        $matches = Pattern::erSuffix()->match($shortName);

        if ($matches) {
            return RuleErrorFactory::createErrorWithTips(
                message: 'Class %s has a name ending with "er", which violates object naming principles (Elegant Object principle).',
                messageParameters: [$className],
                tips: TipFactory::neverUseErNames()->tips(),
            )->errors();
        }

        return [];
    }
}
