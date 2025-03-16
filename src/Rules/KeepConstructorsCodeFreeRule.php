<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\KeepConstructorsCodeFreeAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<KeepConstructorsCodeFreeAnalyzer, Node>
 */
final class KeepConstructorsCodeFreeRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     */
    public function __construct(
        array $excludedPaths = [],
    )
    {
        parent::__construct(new KeepConstructorsCodeFreeAnalyzer(
            $excludedPaths,
        ));
    }
}
