<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\KeepInterfacesShortAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use PhpParser\Node\Stmt\Interface_;

/**
 * @extends ComposableRule<KeepInterfacesShortAnalyzer, Interface_>
 */
final class KeepInterfacesShortRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param int $maxMethods
     */
    public function __construct(
        array $excludedPaths = [],
        int $maxMethods = 5,
    ) {
        parent::__construct(
            new KeepInterfacesShortAnalyzer(
                excludedPaths: $excludedPaths,
                maxMethods: $maxMethods,
            )
        );
    }
}
