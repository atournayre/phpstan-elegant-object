<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Rules\NeverReturnNullRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NeverReturnNullRule<NodeAnalyzerInterface, Node>>
 */
class NeverReturnNullRuleTest extends RuleTestCase
{
    protected function getRule(): NeverReturnNullRule
    {
        return new NeverReturnNullRule(
            excludedPaths: ['/excluded/path'],
            allowedMethodNames: ['allowedMethod'],
            allowedInterfaces: ['NullReturnInterface', 'Namespace\*\NullReturnInterface', '*Interface']
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NeverReturnNull/ReturnNullClass.php'], [
            [
                sprintf(
                    'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverReturnNull\ReturnNullClass::returnNull() returns null, which violates object encapsulation (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::neverReturnNull()->tips()[0],
                ),
                13,
            ],
            [
                sprintf(
                    'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverReturnNull\ReturnNullClass::returnNullWithCondition() returns null, which violates object encapsulation (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::neverReturnNull()->tips()[0],
                ),
                26,
            ],
            [
                sprintf(
                    'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverReturnNull\ReturnNullClass::returnNullWithTernary() returns null, which violates object encapsulation (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::neverReturnNull()->tips()[0],
                ),
                34,
            ],
        ]);
    }

    public function testAllowedMethodNames(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NeverReturnNull/AllowedMethodClass.php'], []);
    }

//    public function testExcludedPaths(): void
//    {
//        $this->analyse(['/excluded/path/test.php'], []);
//    }

    public function testAllowedInterfaces(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NeverReturnNull/ImplementingClass.php'], []);
    }
}
