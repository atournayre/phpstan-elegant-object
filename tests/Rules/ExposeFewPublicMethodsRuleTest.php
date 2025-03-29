<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Rules\ExposeFewPublicMethodsRule;
use Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ExposeFewPublicMethods\ClassWithTooManyPublicMethods;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ExposeFewPublicMethodsRule>
 */
class ExposeFewPublicMethodsRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ExposeFewPublicMethodsRule(
            excludedPaths: [],
            maxPublicMethods: 5,
            secondaryConstructorPrefixes: ['new', 'from', 'create', 'of', 'with'],
        );
    }

    public function testRule(): void
    {
        $this->analyse(
            [
                __DIR__ . '/../Fixtures/ExposeFewPublicMethods/ClassWithFewPublicMethods.php',
            ],
            []
        );

        $this->analyse(
            [
                __DIR__ . '/../Fixtures/ExposeFewPublicMethods/ClassWithTooManyPublicMethods.php',
            ],
            [
                [
                    sprintf(
                        'Class %s has 7 public methods, which exceeds the maximum of 5 (Elegant Object principle).%s    💡 %s',
                        ClassWithTooManyPublicMethods::class,
                        PHP_EOL,
                        TipFactory::exposeFewPublicMethods()->tips()[0],
                    ),
                    10,
                ],
            ]
        );
    }
}
