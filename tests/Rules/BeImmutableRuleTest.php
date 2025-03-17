<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Rules\BeImmutableRule;
use Atournayre\PHPStan\ElegantObject\Tests\Fixtures\BeImmutable\MutableInterface;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<BeImmutableRule<NodeAnalyzerInterface, Node>>
 */
class BeImmutableRuleTest extends RuleTestCase
{
    protected function getRule(): BeImmutableRule
    {
        return new BeImmutableRule(
            excludedPaths: ['/excluded/path'],
            allowedPropertyNames: ['allowedMutableProperty'],
            allowedInterfaces: [MutableInterface::class, 'Namespace\*\MutableInterface', '*Interface']
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/BeImmutable/MutableClass.php'], [
            [
                sprintf(
                    'Property Atournayre\PHPStan\ElegantObject\Tests\Fixtures\BeImmutable\MutableClass::$mutableProperty is mutable, which violates object immutability (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::beImmutable()->tips()[0],
                ),
                11,
            ],
            [
                sprintf(
                    'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\BeImmutable\MutableClass::modifyProperty() modifies an object property, which violates object immutability (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::beImmutable()->tips()[0],
                ),
                22,
            ],
        ]);
    }

    public function testAllowedPropertyNames(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/BeImmutable/AllowedPropertyClass.php'], []);
    }

//    public function testExcludedPaths(): void
//    {
//        $this->analyse(['/excluded/path/test.php'], []);
//    }

    public function testAllowedInterfaces(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/BeImmutable/ImplementingClass.php'], []);
    }

    public function testImmutableClass(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/BeImmutable/ImmutableClass.php'], []);
    }
}
