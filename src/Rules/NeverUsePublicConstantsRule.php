<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\NeverUsePublicConstantsAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<NeverUsePublicConstantsAnalyzer, Node>
 */
final  class NeverUsePublicConstantsRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedClasses
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedClasses = []
    ) {
        parent::__construct(new NeverUsePublicConstantsAnalyzer(
            $excludedPaths,
            $allowedClasses
        ));
    }

}
