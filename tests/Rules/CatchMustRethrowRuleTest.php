<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\CatchMustRethrowRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<CatchMustRethrowRule<NodeAnalyzerInterface, Node>>
 */
class CatchMustRethrowRuleTest extends RuleTestCase
{
    /** @var array<string, array<string>> */
    private array $configuration = [];

    protected function getRule(): CatchMustRethrowRule
    {
        return new CatchMustRethrowRule(...$this->configuration);
    }

    private function message(string $types): string
    {
        return sprintf('Catch of %s must chain the caught exception and rethrow it.', $types);
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/CatchMustRethrow/Example.php'], [
            [$this->message('Exception'), 40],
            [$this->message('InvalidArgumentException|JsonException'), 49],
            [$this->message('Exception'), 57],
            [$this->message('Exception'), 66],
        ]);
    }

    public function testConfiguredExemptions(): void
    {
        $ns = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\CatchMustRethrow\\';
        $this->configuration = [
            'allowedExceptions' => ['Json*'],
            'allowedMethodNames' => ['allowedMethod'],
            'allowedClasses' => [$ns . 'AllowedByWildcard*'],
            'allowedInterfaces' => [$ns . 'TolerantInterface'],
        ];

        $this->analyse([__DIR__ . '/../Fixtures/CatchMustRethrow/Configurable.php'], [
            [$this->message('Exception'), 13],
        ]);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $dir = (string) realpath(__DIR__ . '/../Fixtures/CatchMustRethrow');
        $this->configuration = ['excludedPaths' => [$dir . '/ExcludedClass']];

        $this->analyse([__DIR__ . '/../Fixtures/CatchMustRethrow/ExcludedClass.php'], []);
    }

    public function testTestClassIsSkipped(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/CatchMustRethrow/TestHelper.php'], []);
    }
}
