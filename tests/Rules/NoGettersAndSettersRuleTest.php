<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Rules\NoGettersAndSettersRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoGettersAndSettersRule<NodeAnalyzerInterface, Node>>
 */
class NoGettersAndSettersRuleTest extends RuleTestCase
{
    protected function getRule(): NoGettersAndSettersRule
    {
        return new NoGettersAndSettersRule(
            excludedPaths: ['/excluded/path'],
            allowedMethodNames: ['getSomethingAllowed'],
            allowedInterfaces: ['AllowedInterface', 'Namespace\*\AllowedInterface', '*Interface']
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoGettersAndSetters/GettersSetters.php'], [
            [
                sprintf(
                    'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoGettersAndSetters\GettersSetters::getName() appears to be a getter, which violates object encapsulation (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::gettersAndSetters()->tips()[0],
                ),
                10,
            ],
            [
                sprintf(
                    'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoGettersAndSetters\GettersSetters::setName() appears to be a setter, which violates object encapsulation (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::gettersAndSetters()->tips()[0],
                ),
                15,
            ],
        ]);
    }

    public function testAllowedMethodNames(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoGettersAndSetters/AllowedMethodsClass.php'], []);
    }

//    public function testExcludedPaths(): void
//    {
//        $this->analyse(['/excluded/path/test.php'], []);
//    }

    public function testAllowedInterfaces(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoGettersAndSetters/ImplementingClass.php'], []);
    }
}