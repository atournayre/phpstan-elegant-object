<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\TestClassTrait;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\ShouldNotHappenException;

/**
 * Encapsulate as little as possible: a class holds a few properties only.
 *
 * Counts declared instance properties, promoted constructor properties included.
 * Static properties and constants are ignored. The threshold is a configurable guess (default 4).
 */
final class MaxPropertiesAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use InterfaceExceptionTrait;
    use TestClassTrait;

    /** @var array<string> */
    private array $allowedClasses;

    private int $maxProperties;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedClasses
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        int $maxProperties = 4,
        array $allowedClasses = [],
        array $allowedInterfaces = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->maxProperties = $maxProperties;
        $this->allowedClasses = $allowedClasses;
        $this->allowedInterfaces = $allowedInterfaces;
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        // InClassNode is the documented way to get the class reflection while visiting a class declaration.
        // @phpstan-ignore phpstanApi.instanceofAssumption
        if (!$node instanceof InClassNode || $this->isExcludedPath($scope->getFile())) {
            return true;
        }

        $class = $node->getOriginalNode();
        if (!$class instanceof Class_ || $class->isAnonymous()) {
            return true;
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null || $this->isTestClass($classReflection)) {
            return true;
        }

        return $this->isAllowedClass($classReflection->getName()) || $this->hasAllowedInterface($classReflection);
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        // @phpstan-ignore phpstanApi.instanceofAssumption
        $class = $node instanceof InClassNode ? $node->getOriginalNode() : null;
        $classReflection = $scope->getClassReflection();
        if (!$class instanceof Class_ || $classReflection === null) {
            return [];
        }

        $count = $this->countProperties($class);
        if ($count <= $this->maxProperties) {
            return [];
        }

        return RuleErrorFactory::createErrorWithTips(
            'Class %s has %d properties, which exceeds the maximum of %d (Elegant Object principle: encapsulate as little as possible).',
            'elegantObject.properties.tooMany',
            [$classReflection->getName(), $count, $this->maxProperties],
            ['Split the class into smaller objects, each encapsulating fewer properties.'],
        )->errors();
    }

    private function countProperties(Class_ $node): int
    {
        $count = 0;
        foreach ($node->getProperties() as $property) {
            if (!$property->isStatic()) {
                $count += count($property->props);
            }
        }

        $constructor = $node->getMethod('__construct');
        if ($constructor === null) {
            return $count;
        }

        foreach ($constructor->getParams() as $param) {
            if ($param->isPromoted()) {
                $count++;
            }
        }

        return $count;
    }

    private function isAllowedClass(string $className): bool
    {
        foreach ($this->allowedClasses as $allowed) {
            $pattern = '/^' . str_replace('\*', '.*', preg_quote($allowed, '/')) . '$/';
            if (preg_match($pattern, $className) === 1) {
                return true;
            }
        }

        return false;
    }
}
