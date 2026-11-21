<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\PrivateStaticLiteralsAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<PrivateStaticLiteralsAnalyzer, Node>
 */
final class PrivateStaticLiteralsRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedLiterals
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedLiterals = [],
        int $minOccurrences = 2,
        int $minLength = 2,
    ) {
        parent::__construct(new PrivateStaticLiteralsAnalyzer(
            $excludedPaths,
            $allowedLiterals,
            $minOccurrences,
            $minLength,
        ));
    }
}
