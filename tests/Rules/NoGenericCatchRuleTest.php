<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\NoGenericCatchRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoGenericCatchRule<NodeAnalyzerInterface, Node>>
 */
class NoGenericCatchRuleTest extends RuleTestCase
{
    /** @var array<string, array<string>> */
    private array $configuration = [];

    protected function getRule(): NoGenericCatchRule
    {
        return new NoGenericCatchRule(...$this->configuration);
    }

    private function message(string $type): string
    {
        return sprintf('Catching generic %s is forbidden. Catch a specific exception instead.', $type);
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoGenericCatch/Example.php'], [
            [$this->message('Exception'), 13],
            [$this->message('Throwable'), 21],
            [$this->message('Error'), 29],
            [$this->message('RuntimeException'), 29],
            [$this->message('LogicException'), 29],
            [$this->message('Throwable'), 37],
        ]);
    }

    public function testConfiguredExemptions(): void
    {
        $ns = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoGenericCatch\\';
        $this->configuration = [
            'allowedExceptions' => ['Runtime*'],
            'allowedMethodNames' => ['allowedMethod'],
            'allowedClasses' => [$ns . 'AllowedByWildcard*'],
            'allowedInterfaces' => [$ns . 'TolerantInterface'],
        ];

        $this->analyse([__DIR__ . '/../Fixtures/NoGenericCatch/Configurable.php'], [
            [$this->message('Exception'), 13],
        ]);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->configuration = ['excludedPaths' => [(string) realpath(__DIR__ . '/../Fixtures/NoGenericCatch') . '/ExcludedClass']];

        $this->analyse([__DIR__ . '/../Fixtures/NoGenericCatch/ExcludedClass.php'], []);
    }

    public function testTestClassIsSkipped(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoGenericCatch/TestHelper.php'], []);
    }
}
