<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Rules\KeepInterfacesShortRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<KeepInterfacesShortRule>
 */
class KeepInterfacesShortRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new KeepInterfacesShortRule(
            excludedPaths: ['tests/data/excluded'],
            maxMethods: 3
        );
    }

    public function testRuleShortInterface(): void
    {
        $this->analyse(
            [__DIR__ . '/../Fixtures/KeepInterfacesShort/ShortInterface.php'],
            []
        );
    }

    public function testRuleLongInterface(): void
    {
        $this->analyse(
            [__DIR__ . '/../Fixtures/KeepInterfacesShort/LongInterface.php'],
            [
                [
                    sprintf(
                        'Interface Atournayre\PHPStan\ElegantObject\Tests\Fixtures\KeepInterfacesShort\LongInterface has 4 methods, which exceeds the maximum of 3 (Elegant Object principle).%s    💡 %s',
                        PHP_EOL,
                        TipFactory::keepInterfacesShort()->tips()[0],
                    ),
                    10
                ],
            ]
        );
    }

    public function testRuleEmptyInterface(): void
    {
        $this->analyse(
            [__DIR__ . '/../Fixtures/KeepInterfacesShort/EmptyInterface.php'],
            []
        );
    }

    public function testRuleVeryLongInterface(): void
    {
        $this->analyse(
            [__DIR__ . '/../Fixtures/KeepInterfacesShort/VeryLongInterface.php'],
            [
                [
                    sprintf(
                        'Interface Atournayre\PHPStan\ElegantObject\Tests\Fixtures\KeepInterfacesShort\VeryLongInterface has 8 methods, which exceeds the maximum of 3 (Elegant Object principle).%s    💡 %s',
                        PHP_EOL,
                        TipFactory::keepInterfacesShort()->tips()[0],
                    ),
                    9
                ],
            ]
        );
    }
}
