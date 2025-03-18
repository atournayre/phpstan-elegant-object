<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\NeverAcceptNullArgumentsAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<NeverAcceptNullArgumentsAnalyzer, Node>
 */
final class NeverAcceptNullArgumentsRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedParameterNames
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedMethodNames = [],
        array $allowedParameterNames = [],
        array $allowedInterfaces = [],
    ) {
        parent::__construct(new NeverAcceptNullArgumentsAnalyzer(
            $excludedPaths,
            $allowedMethodNames,
            $allowedParameterNames,
            $allowedInterfaces,
        ));
    }
}
