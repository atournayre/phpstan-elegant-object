<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\NoStaticPropertiesRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoStaticPropertiesRule<NodeAnalyzerInterface, Node>>
 */
class NoStaticPropertiesRuleTest extends RuleTestCase
{
    private const NS = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoStaticProperties\\';

    /** @var array<string, array<string>> */
    private array $configuration = [];

    protected function getRule(): NoStaticPropertiesRule
    {
        return new NoStaticPropertiesRule(...$this->configuration);
    }

    private function fixture(string $name): string
    {
        return __DIR__ . '/../Fixtures/NoStaticProperties/' . $name . '.php';
    }

    public function testRule(): void
    {
        $class = self::NS . 'Example';
        $this->analyse([$this->fixture('Example')], [
            ["Static property $class::\$counter is global state. Inject the state instead.", 9],
            ["Static property $class::\$instance is global state. Inject the state instead.", 10],
            ["Static property $class::\$first is global state. Inject the state instead.", 11],
            ["Static property $class::\$second is global state. Inject the state instead.", 11],
            ["Static variable \$calls in $class::remember() keeps hidden state between calls. Use an object property instead.", 17],
            ["Global variable \$config used in $class::read() is global state. Inject it instead.", 24],
            ["\$GLOBALS used in $class::superGlobal() is global state. Inject the data instead.", 31],
        ]);
    }

    public function testConfiguredExemptions(): void
    {
        $this->configuration = [
            'allowedPropertiesNames' => ['allowedName'],
            'allowedClasses' => [self::NS . 'AllowedByWildcard*'],
            'allowedInterfaces' => [self::NS . 'TolerantInterface'],
        ];

        $this->analyse([
            $this->fixture('Configurable'),
            $this->fixture('AllowedByWildcardClass'),
            $this->fixture('AllowedByInterface'),
        ], [
            ['Static property ' . self::NS . 'Configurable::$reported is global state. Inject the state instead.', 9],
        ]);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->configuration = ['excludedPaths' => [dirname(__DIR__) . '/Fixtures/NoStaticProperties/ExcludedClass']];

        $this->analyse([$this->fixture('ExcludedClass')], []);
    }

    public function testTestClassIsSkipped(): void
    {
        $this->analyse([$this->fixture('TestDoubleExample')], []);
    }
}
