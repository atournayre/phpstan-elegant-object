<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject;

use Atournayre\PHPStan\ElegantObject\Analyzer\RuleAnalyzer;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @implements Rule<TNodeType>
 */
class ComposableRule implements Rule
{
    public function __construct(
        protected NodeAnalyzerInterface $analyzer,
    )
    {
    }

    /**
     * @return class-string<TNodeType>
     */
    public function getNodeType(): string
    {
        if ($this->analyzer instanceof RuleAnalyzer) {
            /** @var class-string<TNodeType> */
            return $this->analyzer->getNodeType();
        }

        throw new \LogicException('Analyzer must implement getNodeType() method');
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->analyzer instanceof RuleAnalyzer && $this->analyzer->shouldSkipAnalysis($node, $scope)) {
            return [];
        }

        return $this->analyzer->analyze($node, $scope);
    }
}
