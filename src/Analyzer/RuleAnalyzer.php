<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;
use PHPStan\Analyser\Scope;

abstract class RuleAnalyzer implements NodeAnalyzerInterface
{
    abstract public function getNodeType(): string;

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        return false;
    }
}
