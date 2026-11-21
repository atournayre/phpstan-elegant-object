<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\NoStaticPropertiesAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<NoStaticPropertiesAnalyzer, Node>
 */
final class NoStaticPropertiesRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedPropertiesNames
     * @param array<string> $allowedInterfaces
     * @param array<string> $allowedClasses
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedPropertiesNames = [],
        array $allowedInterfaces = [],
        array $allowedClasses = [],
    ) {
        parent::__construct(new NoStaticPropertiesAnalyzer(
            $excludedPaths,
            $allowedPropertiesNames,
            $allowedInterfaces,
            $allowedClasses,
        ));
    }
}
