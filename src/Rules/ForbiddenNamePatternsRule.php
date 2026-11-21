<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\ForbiddenNamePatternsAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<ForbiddenNamePatternsAnalyzer, Node>
 */
final class ForbiddenNamePatternsRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $forbiddenPatterns
     * @param array<string> $allowedClasses
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $forbiddenPatterns = [],
        array $allowedClasses = [],
        array $allowedInterfaces = [],
    ) {
        parent::__construct(new ForbiddenNamePatternsAnalyzer(
            $excludedPaths,
            $forbiddenPatterns,
            $allowedClasses,
            $allowedInterfaces,
        ));
    }
}
