<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Contract;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;

interface NodeAnalyzerInterface
{
    /**
     * @return list<IdentifierRuleError> Errors detected
     */
    public function analyze(Node $node, Scope $scope): array;
}
