<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\MaxPropertiesRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<MaxPropertiesRule<NodeAnalyzerInterface, Node>>
 */
class MaxPropertiesRuleTest extends RuleTestCase
{
    private const NS = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\MaxProperties\\';
    private const TIP = 'Split the class into smaller objects, each encapsulating fewer properties.';

    /** @var array{excludedPaths?: array<string>, maxProperties?: int, allowedClasses?: array<string>, allowedInterfaces?: array<string>} */
    private array $configuration = [];

    protected function getRule(): MaxPropertiesRule
    {
        return new MaxPropertiesRule(...$this->configuration);
    }

    private function message(string $class, int $count, int $max = 4): string
    {
        return sprintf(
            'Class %s%s has %d properties, which exceeds the maximum of %d (Elegant Object principle: encapsulate as little as possible).%s    💡 %s',
            self::NS,
            $class,
            $count,
            $max,
            PHP_EOL,
            self::TIP,
        );
    }

    public function testReportsClassWithTooManyProperties(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/MaxProperties/TooManyProperties.php'], [
            [$this->message('TooManyProperties', 5), 7],
        ]);
    }

    public function testCountsEveryPropertyOfAStatement(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/MaxProperties/MultiplePropertiesPerStatement.php'], [
            [$this->message('MultiplePropertiesPerStatement', 5), 7],
        ]);
    }

    public function testIgnoresStaticPropertiesConstantsAndPlainParameters(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/MaxProperties/FewProperties.php'], []);
    }

    public function testConfiguredThreshold(): void
    {
        $this->configuration = ['maxProperties' => 5];
        $this->analyse([__DIR__ . '/../Fixtures/MaxProperties/TooManyProperties.php'], []);
    }

    public function testExcludedPath(): void
    {
        $this->configuration = ['excludedPaths' => [(string) realpath(__DIR__ . '/../Fixtures/MaxProperties')]];
        $this->analyse([__DIR__ . '/../Fixtures/MaxProperties/ExcludedClass.php'], []);
    }

    public function testAllowedClassWithWildcard(): void
    {
        $this->configuration = ['allowedClasses' => [self::NS . 'Excluded*']];
        $this->analyse([__DIR__ . '/../Fixtures/MaxProperties/ExcludedClass.php'], []);
    }

    public function testAllowedInterface(): void
    {
        $this->configuration = ['allowedInterfaces' => [self::NS . 'MarkerInterface']];
        $this->analyse([
            __DIR__ . '/../Fixtures/MaxProperties/MarkerInterface.php',
            __DIR__ . '/../Fixtures/MaxProperties/ImplementsMarker.php',
        ], []);
    }

    public function testTestClassesAreSkipped(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/MaxProperties/TestDoubleWithManyProperties.php'], []);
    }
}
