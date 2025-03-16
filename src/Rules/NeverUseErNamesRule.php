<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\NeverUseErNamesAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<NeverUseErNamesAnalyzer, Node>
 */
final class NeverUseErNamesRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $excludedSuffixes
     */
    public function __construct(
        array $excludedPaths = [],
        array $excludedSuffixes = [],
    )
    {
        parent::__construct(new NeverUseErNamesAnalyzer(
            $excludedPaths,
            $excludedSuffixes,
        ));
    }
}
