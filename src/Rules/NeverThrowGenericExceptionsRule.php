<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\NeverThrowGenericExceptionsAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * Opt-in project convention (not an Elegant Objects rule): deviates from EO 4.2.5.
 *
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<NeverThrowGenericExceptionsAnalyzer, Node>
 */
final class NeverThrowGenericExceptionsRule extends ComposableRule
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
        ?string $requiredInterface = null,
    ) {
        parent::__construct(new NeverThrowGenericExceptionsAnalyzer(
            $excludedPaths,
            $allowedExceptions,
            $allowedMethodNames,
            $allowedClasses,
            $requiredInterface,
        ));
    }
}
