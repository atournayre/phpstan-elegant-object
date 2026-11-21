<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\NoPrimitiveParameterInServiceRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoPrimitiveParameterInServiceRule<NodeAnalyzerInterface, Node>>
 */
class NoPrimitiveParameterInServiceRuleTest extends RuleTestCase
{
    protected function getRule(): NoPrimitiveParameterInServiceRule
    {
        return new NoPrimitiveParameterInServiceRule(['Fixtures/NoPrimitiveParameterInService/ExcludedService']);
    }

    public function testRule(): void
    {
        $message = 'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoPrimitiveParameterInService\ExampleService::%s() of a service accepts primitive parameters (%s). Wrap the values in value objects.';

        $this->analyse([__DIR__ . '/../Fixtures/NoPrimitiveParameterInService/ExampleService.php'], [
            [sprintf($message, 'plain', '$name'), 13],
            [sprintf($message, 'nullable', '$number'), 17],
            [sprintf($message, 'union', '$id'), 21],
            [sprintf($message, 'untyped', '$value'), 25],
            [sprintf($message, 'listing', '$items, $others'), 29],
            [sprintf($message, '__invoke', '$value'), 53],
        ]);
    }

    public function testNonServicesAndEntitiesAreSkipped(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoPrimitiveParameterInService/NotAService.php'], []);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoPrimitiveParameterInService/ExcludedService.php'], []);
    }
}
