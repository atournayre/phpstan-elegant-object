<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\NoNullUsageAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<NoNullUsageAnalyzer, Node>
 */
final class NoNullUsageRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedPropertyNames
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedPropertyNames = [],
        array $allowedMethodNames = [],
        array $allowedInterfaces = [],
        bool $checkProperties = true,
        bool $checkComparisons = true,
        bool $checkNullsafe = true,
    ) {
        parent::__construct(new NoNullUsageAnalyzer(
            $excludedPaths,
            $allowedPropertyNames,
            $allowedMethodNames,
            $allowedInterfaces,
            $checkProperties,
            $checkComparisons,
            $checkNullsafe,
        ));
    }
}
