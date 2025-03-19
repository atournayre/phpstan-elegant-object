<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\NoNewOutsideSecondaryConstructorsAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<NoNewOutsideSecondaryConstructorsAnalyzer, Node>
 */
final class NoNewOutsideSecondaryConstructorsRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedInterfaces
     * @param array<string> $excludedClasses
     * @param array<string> $secondaryConstructorPrefixes
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedInterfaces = [],
        array $excludedClasses = [],
        array $secondaryConstructorPrefixes = ['new', 'from', 'create', 'of', 'with'],
    )
    {
        parent::__construct(new NoNewOutsideSecondaryConstructorsAnalyzer(
            $excludedPaths,
            $allowedInterfaces,
            $excludedClasses,
            $secondaryConstructorPrefixes,
        ));
    }
}
