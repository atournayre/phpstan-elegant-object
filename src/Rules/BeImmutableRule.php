<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\PropertyModificationAnalyzer;
use Atournayre\PHPStan\ElegantObject\Analyzer\PropertyMutabilityAnalyzer;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\ShouldNotHappenException;

/**
 * @implements Rule<Node>
 */
final class BeImmutableRule implements Rule
{
    private PropertyMutabilityAnalyzer $propertyAnalyzer;
    private PropertyModificationAnalyzer $modificationAnalyzer;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedPropertyNames
     * @param array<string> $allowedInterfaces
     * @param array<string> $allowedMethodNames
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedPropertyNames = [],
        array $allowedInterfaces = [],
        array $allowedMethodNames = [],
    ) {
        $this->propertyAnalyzer = new PropertyMutabilityAnalyzer(
            $excludedPaths,
            $allowedPropertyNames,
            $allowedInterfaces,
        );

        $this->modificationAnalyzer = new PropertyModificationAnalyzer(
            $excludedPaths,
            $allowedMethodNames,
            $allowedPropertyNames,
            $allowedInterfaces,
        );
    }

    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @param Node $node
     * @param Scope $scope
     *
     * @return array<RuleError>
     * @throws ShouldNotHappenException
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // Check property declarations
        if (is_a($node, $this->propertyAnalyzer->getNodeType())) {
            if (!$this->propertyAnalyzer->shouldSkipAnalysis($node, $scope)) {
                return $this->propertyAnalyzer->analyze($node, $scope);
            }
        }

        // Check property modifications
        if (is_a($node, $this->modificationAnalyzer->getNodeType())) {
            if (!$this->modificationAnalyzer->shouldSkipAnalysis($node, $scope)) {
                return $this->modificationAnalyzer->analyze($node, $scope);
            }
        }

        return [];
    }
}
