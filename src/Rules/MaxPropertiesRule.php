<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\MaxPropertiesAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<MaxPropertiesAnalyzer, Node>
 */
final class MaxPropertiesRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedClasses
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        int $maxProperties = 4,
        array $allowedClasses = [],
        array $allowedInterfaces = [],
    ) {
        parent::__construct(new MaxPropertiesAnalyzer(
            $excludedPaths,
            $maxProperties,
            $allowedClasses,
            $allowedInterfaces,
        ));
    }
}
