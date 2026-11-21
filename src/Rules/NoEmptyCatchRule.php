<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\NoEmptyCatchAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<NoEmptyCatchAnalyzer, Node>
 */
final class NoEmptyCatchRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedExceptions
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedClasses
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedExceptions = [],
        array $allowedMethodNames = [],
        array $allowedClasses = [],
    ) {
        parent::__construct(new NoEmptyCatchAnalyzer(
            $excludedPaths,
            $allowedExceptions,
            $allowedMethodNames,
            $allowedClasses,
        ));
    }
}
