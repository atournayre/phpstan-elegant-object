<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\ConstructorsMustBePrivateAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<ConstructorsMustBePrivateAnalyzer, Node>
 */
final class ConstructorsMustBePrivateRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $excludedNamespaces
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $excludedNamespaces = [],
        array $allowedInterfaces = [],
    ) {
        parent::__construct(new ConstructorsMustBePrivateAnalyzer($excludedPaths, $excludedNamespaces, $allowedInterfaces));
    }
}
