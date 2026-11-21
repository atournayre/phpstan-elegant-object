<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\NoNullUsageRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoNullUsageRule<NodeAnalyzerInterface, Node>>
 */
class NoNullUsageRuleTest extends RuleTestCase
{
    private const NS = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoNullUsage\\';
    private const TIP = 'Use a Null Object or an exception instead of null (Elegant Object principle, chapter 4.1).';

    /** @var array<string> */
    private array $excludedPaths = [];

    /** @var array<string> */
    private array $allowedPropertyNames = [];

    /** @var array<string> */
    private array $allowedMethodNames = [];

    /** @var array<string> */
    private array $allowedInterfaces = [];

    private bool $checkProperties = true;

    private bool $checkComparisons = true;

    private bool $checkNullsafe = true;

    protected function getRule(): NoNullUsageRule
    {
        return new NoNullUsageRule(
            $this->excludedPaths,
            $this->allowedPropertyNames,
            $this->allowedMethodNames,
            $this->allowedInterfaces,
            $this->checkProperties,
            $this->checkComparisons,
            $this->checkNullsafe,
        );
    }

    /**
     * @return array{string, int, string}
     */
    private function property(string $name, int $line, string $kind = 'is nullable'): array
    {
        return [sprintf('Property %sExample::$%s %s.', self::NS, $name, $kind), $line, self::TIP];
    }

    /**
     * @return array{string, int, string}
     */
    private function method(string $method, string $what, int $line): array
    {
        return [sprintf('Method %sExample::%s() %s.', self::NS, $method, $what), $line, self::TIP];
    }

    /**
     * @return array{string, int, string}
     */
    private function comparison(string $method, int $line): array
    {
        return $this->method($method, 'compares a value with null', $line);
    }

    /**
     * @return array{string, int, string}
     */
    private function nullsafe(int $line): array
    {
        return $this->method('nullsafe', 'uses the null-safe operator', $line);
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoNullUsage/Example.php'], [
            $this->property('nullable', 9),
            $this->property('union', 10),
            $this->property('untyped', 11, 'defaults to null'),
            $this->property('allowedProperty', 13),
            $this->comparison('comparisons', 21),
            $this->comparison('comparisons', 22),
            $this->comparison('comparisons', 23),
            $this->comparison('comparisons', 24),
            $this->comparison('comparisons', 26),
            $this->nullsafe(31),
            $this->nullsafe(33),
            $this->comparison('allowedMethod', 45),
        ]);
    }

    public function testConfiguredExemptionsAndToggles(): void
    {
        $this->allowedPropertyNames = ['allowedProperty'];
        $this->allowedMethodNames = ['allowedMethod'];
        $this->checkComparisons = false;

        $this->analyse([__DIR__ . '/../Fixtures/NoNullUsage/Example.php'], [
            $this->property('nullable', 9),
            $this->property('union', 10),
            $this->property('untyped', 11, 'defaults to null'),
            $this->nullsafe(31),
            $this->nullsafe(33),
        ]);
    }

    public function testDisabledChecks(): void
    {
        $this->checkProperties = false;
        $this->checkNullsafe = false;

        $this->analyse([__DIR__ . '/../Fixtures/NoNullUsage/Example.php'], [
            $this->comparison('comparisons', 21),
            $this->comparison('comparisons', 22),
            $this->comparison('comparisons', 23),
            $this->comparison('comparisons', 24),
            $this->comparison('comparisons', 26),
            $this->comparison('allowedMethod', 45),
        ]);
    }

    public function testExcludedPathTestClassAndInterfaceAreSkipped(): void
    {
        $this->excludedPaths = ['Fixtures/NoNullUsage/ExcludedClass'];
        $this->allowedInterfaces = [self::NS . 'TolerantInterface'];

        $this->analyse([
            __DIR__ . '/../Fixtures/NoNullUsage/ExcludedClass.php',
            __DIR__ . '/../Fixtures/NoNullUsage/TestDouble.php',
            __DIR__ . '/../Fixtures/NoNullUsage/TolerantClass.php',
        ], []);
    }
}
