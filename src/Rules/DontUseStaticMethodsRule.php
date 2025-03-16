<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\DontUseStaticMethodsAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<DontUseStaticMethodsAnalyzer, Node>
 */
final class DontUseStaticMethodsRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedMethodNames = [],
        array $allowedInterfaces = []
    ) {
        parent::__construct(new DontUseStaticMethodsAnalyzer(
            $excludedPaths,
            $allowedMethodNames,
            $allowedInterfaces
        ));
    }
}
