<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\AvoidTypeIntrospectionAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<AvoidTypeIntrospectionAnalyzer, Node>
 */
final class AvoidTypeIntrospectionRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedInterfaces
     * @param array<string> $allowedClasses
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedMethodNames = [],
        array $allowedInterfaces = [],
        array $allowedClasses = [],
    ) {
        parent::__construct(new AvoidTypeIntrospectionAnalyzer(
            $excludedPaths,
            $allowedMethodNames,
            $allowedInterfaces,
            $allowedClasses,
        ));
    }
}
