<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\InterfacesMustHaveSuffixAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<InterfacesMustHaveSuffixAnalyzer, Node>
 */
final class InterfacesMustHaveSuffixRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $excludedNamespaces
     * @param array<string> $allowedClasses
     */
    public function __construct(
        array $excludedPaths = [],
        array $excludedNamespaces = [],
        array $allowedClasses = [],
        string $suffix = 'Interface',
        bool $forbidPrefix = false,
    ) {
        parent::__construct(new InterfacesMustHaveSuffixAnalyzer(
            $excludedPaths,
            $excludedNamespaces,
            $allowedClasses,
            $suffix,
            $forbidPrefix,
        ));
    }
}
