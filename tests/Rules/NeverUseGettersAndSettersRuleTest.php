<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Rules\NeverUseGettersAndSettersRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NeverUseGettersAndSettersRule<NodeAnalyzerInterface, Node>>
 */
class NeverUseGettersAndSettersRuleTest extends RuleTestCase
{
    protected function getRule(): NeverUseGettersAndSettersRule
    {
        return new NeverUseGettersAndSettersRule(
            excludedPaths: ['/excluded/path'],
            allowedMethodNames: ['getSomethingAllowed'],
            allowedInterfaces: ['AllowedInterface', 'Namespace\*\AllowedInterface', '*Interface']
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NeverUseGettersAndSetters/GettersSetters.php'], [
            [
                sprintf(
                    'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverUseGettersAndSetters\GettersSetters::getName() appears to be a getter, which violates object encapsulation (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::gettersAndSetters()->tips()[0],
                ),
                10,
            ],
            [
                sprintf(
                    'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverUseGettersAndSetters\GettersSetters::setName() appears to be a setter, which violates object encapsulation (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::gettersAndSetters()->tips()[0],
                ),
                15,
            ],
        ]);
    }

    public function testAllowedMethodNames(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NeverUseGettersAndSetters/AllowedMethodsClass.php'], []);
    }

//    public function testExcludedPaths(): void
//    {
//        $this->analyse(['/excluded/path/test.php'], []);
//    }

    public function testAllowedInterfaces(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NeverUseGettersAndSetters/ImplementingClass.php'], []);
    }
}