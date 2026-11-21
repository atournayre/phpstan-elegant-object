<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\NoProtectedMembersAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<NoProtectedMembersAnalyzer, Node>
 */
final class NoProtectedMembersRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedPropertyNames
     * @param array<string> $allowedInterfaces
     * @param array<string> $allowedClasses
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedMethodNames = [],
        array $allowedPropertyNames = [],
        array $allowedInterfaces = [],
        array $allowedClasses = [],
        bool $allowInAbstractClasses = false,
    ) {
        parent::__construct(new NoProtectedMembersAnalyzer(
            $excludedPaths,
            $allowedMethodNames,
            $allowedPropertyNames,
            $allowedInterfaces,
            $allowedClasses,
            $allowInAbstractClasses,
        ));
    }
}
