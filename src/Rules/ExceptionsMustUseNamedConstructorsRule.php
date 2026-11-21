<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\ExceptionsMustUseNamedConstructorsAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * Project convention (opt-in), not an Elegant Objects rule.
 *
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<ExceptionsMustUseNamedConstructorsAnalyzer, Node>
 */
final class ExceptionsMustUseNamedConstructorsRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $excludedNamespaces
     * @param array<string> $allowedClasses
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $excludedNamespaces = [],
        array $allowedClasses = [],
        array $allowedInterfaces = [],
    ) {
        parent::__construct(new ExceptionsMustUseNamedConstructorsAnalyzer(
            $excludedPaths,
            $excludedNamespaces,
            $allowedClasses,
            $allowedInterfaces,
        ));
    }
}
