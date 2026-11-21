<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\NoGenericCatchAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<NoGenericCatchAnalyzer, Node>
 */
final class NoGenericCatchRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedExceptions
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedClasses
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedExceptions = [],
        array $allowedMethodNames = [],
        array $allowedClasses = [],
        array $allowedInterfaces = [],
    ) {
        parent::__construct(new NoGenericCatchAnalyzer(
            $excludedPaths,
            $allowedExceptions,
            $allowedMethodNames,
            $allowedClasses,
            $allowedInterfaces,
        ));
    }
}
