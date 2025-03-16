<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\AllPropertiesMustBePrivateAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<AllPropertiesMustBePrivateAnalyzer, Node>
 */
final class AllPropertiesMustBePrivateRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedPropertiesNames
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedPropertiesNames = [],
        array $allowedInterfaces = [],
    )
    {
        parent::__construct(new AllPropertiesMustBePrivateAnalyzer(
            $excludedPaths,
            $allowedPropertiesNames,
            $allowedInterfaces,
        ));
    }
}
