<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\NoPrimitiveParameterRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoPrimitiveParameterRule<NodeAnalyzerInterface, Node>>
 */
class NoPrimitiveParameterRuleTest extends RuleTestCase
{
    private const NS = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoPrimitiveParameter\\';

    /** @var array<string, array<string>> */
    private array $configuration = [];

    protected function getRule(): NoPrimitiveParameterRule
    {
        return new NoPrimitiveParameterRule(...$this->configuration);
    }

    private function message(string $class, string $method, string $parameters): string
    {
        return sprintf(
            'Method %s%s::%s() accepts primitive parameters (%s). Wrap the values in value objects.',
            self::NS,
            $class,
            $method,
            $parameters,
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoPrimitiveParameter/Example.php'], [
            [$this->message('Example', 'plain', '$name'), 13],
            [$this->message('Example', 'nullable', '$number'), 17],
            [$this->message('Example', 'union', '$id'), 21],
            [$this->message('Example', 'untyped', '$value'), 25],
            [$this->message('Example', 'listing', '$items, $others'), 29],
            [$this->message('Example', '__invoke', '$value'), 53],
            [$this->message('Contract', 'impose', '$value'), 87],
            [$this->message('Base', 'template', '$value'), 99],
        ]);
    }

    public function testConfiguredExemptions(): void
    {
        $this->configuration = [
            'allowedMethodNames' => ['allowedMethod'],
            'allowedParameterNames' => ['skipped'],
            'allowedTypes' => ['string'],
            'allowedClasses' => [self::NS . 'AllowedBy*Class'],
            'allowedInterfaces' => [self::NS . 'TolerantInterface'],
        ];

        $this->analyse([__DIR__ . '/../Fixtures/NoPrimitiveParameter/Configurable.php'], [
            [$this->message('Configurable', 'allowedParameter', '$reported'), 13],
            [$this->message('Configurable', 'allowedType', '$number'), 17],
        ]);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->configuration = ['excludedPaths' => ['Fixtures/NoPrimitiveParameter/ExcludedClass']];

        $this->analyse([__DIR__ . '/../Fixtures/NoPrimitiveParameter/ExcludedClass.php'], []);
    }
}
