<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\NoEmptyCatchRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoEmptyCatchRule<NodeAnalyzerInterface, Node>>
 */
class NoEmptyCatchRuleTest extends RuleTestCase
{
    /** @var array<string, array<string>> */
    private array $configuration = [];

    protected function getRule(): NoEmptyCatchRule
    {
        return new NoEmptyCatchRule(...$this->configuration);
    }

    private function message(string $types): string
    {
        return sprintf('Catch block for %s swallows the exception. Use it, log it or rethrow it.', $types);
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoEmptyCatch/Example.php'], [
            [$this->message('RuntimeException'), 13],
            [$this->message('RuntimeException'), 21],
            [$this->message('RuntimeException|LogicException'), 30],
            [$this->message('RuntimeException'), 68],
        ]);
    }

    public function testConfiguredExemptions(): void
    {
        $this->configuration = [
            'allowedExceptions' => ['JsonException', 'Ignorable\Fixture\*'],
            'allowedMethodNames' => ['allowedMethod'],
            'allowedClasses' => ['Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoEmptyCatch\AllowedClass'],
        ];

        $this->analyse([__DIR__ . '/../Fixtures/NoEmptyCatch/Configurable.php'], [
            [$this->message('JsonException|RuntimeException'), 29],
        ]);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->configuration = ['excludedPaths' => [(string) realpath(__DIR__ . '/../Fixtures/NoEmptyCatch')]];

        $this->analyse([__DIR__ . '/../Fixtures/NoEmptyCatch/ExcludedClass.php'], []);
    }
}
