<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Rules\NoNewOutsideSecondaryConstructorsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoNewOutsideSecondaryConstructorsRule>
 */
final class NoNewOutsideSecondaryConstructorsRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new NoNewOutsideSecondaryConstructorsRule(
            [],
            ['*Service', '*Factory'],
            ['Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ExcludedClass'],
            ['new', 'from', 'create', 'of', 'with', 'build']
        );
    }

    public function testRule(): void
    {
        $this->analyse([
            __DIR__ . '/../Fixtures/NewOutsideSecondaryConstructors/ValidClass.php',
            __DIR__ . '/../Fixtures/NewOutsideSecondaryConstructors/InvalidClass.php',
            __DIR__ . '/../Fixtures/NewOutsideSecondaryConstructors/ClassWithInterface.php',
            __DIR__ . '/../Fixtures/NewOutsideSecondaryConstructors/ExcludedClass.php',
            __DIR__ . '/../Fixtures/NewOutsideSecondaryConstructors/VariousConstructorPrefixes.php',
        ], [
            [
                sprintf(
                'The operator new is used in Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NewOutsideSecondaryConstructors\InvalidClass::normalMethod() for class DateTime, which violates object encapsulation. Use secondary constructors (static factory methods) instead.%s    💡 %s',
                    PHP_EOL,
                    TipFactory::noNewOutsideSecondaryConstructors()->tips()[0],
                ),
                18,
            ],
            [
                sprintf(
                'The operator new is used in Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NewOutsideSecondaryConstructors\InvalidClass::anotherMethod() for class stdClass, which violates object encapsulation. Use secondary constructors (static factory methods) instead.%s    💡 %s',
                    PHP_EOL,
                    TipFactory::noNewOutsideSecondaryConstructors()->tips()[0],
                ),
                24,
            ],
            [
                sprintf(
                'The operator new is used in Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NewOutsideSecondaryConstructors\VariousConstructorPrefixes::invalidMethod() for class DateTime, which violates object encapsulation. Use secondary constructors (static factory methods) instead.%s    💡 %s',
                    PHP_EOL,
                    TipFactory::noNewOutsideSecondaryConstructors()->tips()[0],
                ),
                31,
            ],
        ]);
    }
}
