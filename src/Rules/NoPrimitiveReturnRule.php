<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\NoPrimitiveReturnAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<NoPrimitiveReturnAnalyzer, Node>
 */
final class NoPrimitiveReturnRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedInterfaces
     * @param array<string> $allowedClasses
     * @param array<string> $allowedTypes
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedMethodNames = [],
        array $allowedInterfaces = [],
        array $allowedClasses = [],
        array $allowedTypes = [],
    ) {
        parent::__construct(new NoPrimitiveReturnAnalyzer(
            $excludedPaths,
            $allowedMethodNames,
            $allowedInterfaces,
            $allowedClasses,
            $allowedTypes,
        ));
    }
}
