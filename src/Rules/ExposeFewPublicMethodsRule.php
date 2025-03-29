<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\ExposeFewPublicMethodsAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use PhpParser\Node\Stmt\Class_;

/**
 * @extends ComposableRule<ExposeFewPublicMethodsAnalyzer, Class_>
 */
final class ExposeFewPublicMethodsRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param int $maxPublicMethods
     * @param array<string> $secondaryConstructorPrefixes
     */
    public function __construct(
        array $excludedPaths = [],
        int $maxPublicMethods = 5,
        array $secondaryConstructorPrefixes = ['new', 'from', 'create', 'of', 'with'],
    ) {
        parent::__construct(
            new ExposeFewPublicMethodsAnalyzer(
                excludedPaths: $excludedPaths,
                maxPublicMethods: $maxPublicMethods,
                secondaryConstructorPrefixes: $secondaryConstructorPrefixes,
            )
        );
    }
}
