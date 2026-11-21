<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Rules;

use Atournayre\PHPStan\ElegantObject\Analyzer\ConstructorObjectsOnlyAnalyzer;
use Atournayre\PHPStan\ElegantObject\ComposableRule;
use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use PhpParser\Node;

/**
 * @template T of NodeAnalyzerInterface
 * @template TNodeType of Node
 * @extends ComposableRule<ConstructorObjectsOnlyAnalyzer, Node>
 */
final class ConstructorObjectsOnlyRule extends ComposableRule
{
    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedParameterNames
     * @param array<string> $allowedInterfaces
     * @param array<string> $allowedClasses
     * @param array<string> $allowedTypes
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedParameterNames = [],
        array $allowedInterfaces = [],
        array $allowedClasses = [],
        array $allowedTypes = [],
        bool $checkPrivateConstructors = true,
    ) {
        parent::__construct(new ConstructorObjectsOnlyAnalyzer(
            $excludedPaths,
            $allowedParameterNames,
            $allowedInterfaces,
            $allowedClasses,
            $allowedTypes,
            $checkPrivateConstructors,
        ));
    }
}
