<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\AlwaysUseInterfaceAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<AlwaysUseInterfaceAnalyzer, Node>
 */
final class AlwaysUseInterfaceRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $excludedClasses
     */
    public function __construct(
        array $excludedPaths = [],
        array $excludedClasses = [],
    )
    {
        parent::__construct(new AlwaysUseInterfaceAnalyzer(
            $excludedPaths,
            $excludedClasses,
        ));
    }
}
