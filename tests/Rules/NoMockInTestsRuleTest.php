<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\NoMockInTestsRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoMockInTestsRule<NodeAnalyzerInterface, Node>>
 */
class NoMockInTestsRuleTest extends RuleTestCase
{
    private const NS = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoMockInTests\\';

    /** @var array<string, array<string>> */
    private array $configuration = [];

    protected function getRule(): NoMockInTestsRule
    {
        return new NoMockInTestsRule(...$this->configuration);
    }

    private function message(string $class, string $method): string
    {
        return sprintf(
            'Test %s%s calls %s() to create a mock. Use a Fake (an in-memory implementation) instead.',
            self::NS,
            $class,
            $method,
        );
    }

    public function testRule(): void
    {
        $class = 'ExampleCase';
        $this->analyse([__DIR__ . '/../Fixtures/NoMockInTests/ExampleCase.php'], [
            [$this->message($class, 'createMock'), 13],
            [$this->message($class, 'createStub'), 14],
            [$this->message($class, 'createConfiguredMock'), 15],
            [$this->message($class, 'createPartialMock'), 16],
            [$this->message($class, 'getMockBuilder'), 17],
            [$this->message($class, 'createMockForIntersectionOfInterfaces'), 18],
            [$this->message($class, 'prophesize'), 19],
            [$this->message($class, 'Mockery::mock'), 20],
            [$this->message($class, 'Mockery::spy'), 21],
        ]);
    }

    public function testNonTestClassIsIgnored(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoMockInTests/Factory.php'], []);
    }

    public function testClassNamedTestIsAnalyzed(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoMockInTests/ByName.php'], [
            [$this->message('ByNameTest', 'createMock'), 16],
        ]);
    }

    public function testConfiguredExemptions(): void
    {
        $this->configuration = [
            'allowedMethodNames' => ['createStub'],
            'allowedClasses' => [self::NS . 'LegacyCase'],
        ];

        $this->analyse([__DIR__ . '/../Fixtures/NoMockInTests/ConfigurableCase.php'], [
            [$this->message('ConfigurableCase', 'createMock'), 14],
        ]);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->configuration = ['excludedPaths' => [dirname(__DIR__) . '/Fixtures/NoMockInTests/ExcludedCase']];

        $this->analyse([__DIR__ . '/../Fixtures/NoMockInTests/ExcludedCase.php'], []);
    }
}
