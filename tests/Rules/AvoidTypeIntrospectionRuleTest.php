<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\AvoidTypeIntrospectionRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<AvoidTypeIntrospectionRule<NodeAnalyzerInterface, Node>>
 */
class AvoidTypeIntrospectionRuleTest extends RuleTestCase
{
    private const NS = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\AvoidTypeIntrospection\\';

    /** @var array<string, array<string>> */
    private array $configuration = [];

    protected function getRule(): AvoidTypeIntrospectionRule
    {
        return new AvoidTypeIntrospectionRule(...$this->configuration);
    }

    private function message(string $class, string $method, string $usages): string
    {
        return sprintf(
            'Method %s%s::%s() uses type introspection or casting (%s). Rely on polymorphism instead.',
            self::NS,
            $class,
            $method,
            $usages,
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/AvoidTypeIntrospection/Example.php'], [
            [$this->message('Example', 'check', 'instanceof'), 9],
            [$this->message('Example', 'functions', 'is_string(), get_class(), gettype()'), 14],
            [$this->message('Example', 'casts', '(int) cast'), 23],
            [$this->message('Example', 'classComparison', '::class comparison'), 28],
            [$this->message('Example', 'staticClass', '::class comparison'), 33],
        ]);
    }

    public function testConfiguredExemptions(): void
    {
        $this->configuration = [
            'allowedMethodNames' => ['allowedMethod'],
            'allowedClasses' => [self::NS . 'AllowedBy*Class'],
            'allowedInterfaces' => [self::NS . 'TolerantInterface'],
        ];

        $this->analyse([__DIR__ . '/../Fixtures/AvoidTypeIntrospection/PlainConfigurable.php'], [
            [$this->message('PlainConfigurable', 'reported', 'is_array()'), 14],
        ]);
        $this->analyse([__DIR__ . '/../Fixtures/AvoidTypeIntrospection/Configurable.php'], []);
        $this->analyse([__DIR__ . '/../Fixtures/AvoidTypeIntrospection/AllowedByWildcardClass.php'], []);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->configuration = ['excludedPaths' => ['Fixtures/AvoidTypeIntrospection/ExcludedClass']];

        $this->analyse([__DIR__ . '/../Fixtures/AvoidTypeIntrospection/ExcludedClass.php'], []);
    }

    public function testTestClassesAreSkipped(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/AvoidTypeIntrospection/InMemoryHandler.php'], []);
    }
}
