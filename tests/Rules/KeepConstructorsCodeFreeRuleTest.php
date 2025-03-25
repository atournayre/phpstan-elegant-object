<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Rules\KeepConstructorsCodeFreeRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<KeepConstructorsCodeFreeRule<NodeAnalyzerInterface, Node>>
 */
class KeepConstructorsCodeFreeRuleTest extends RuleTestCase
{
    protected function getRule(): KeepConstructorsCodeFreeRule
    {
        return new KeepConstructorsCodeFreeRule(
            excludedPaths: ['/excluded/path']
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/KeepConstructorsCodeFree/ClassesWithConstructorCode.php'], [
            [
                sprintf(
                    'Constructor Atournayre\PHPStan\ElegantObject\Tests\Fixtures\KeepConstructorsCodeFree\ClassWithLogicInConstructor::__construct() contains code other than property assignments or assertions, which violates the "code-free constructor" principle (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::keepConstructorsCodeFree()->tips()[0],
                ),
                11,
            ],
            [
                sprintf(
                    'Constructor Atournayre\PHPStan\ElegantObject\Tests\Fixtures\KeepConstructorsCodeFree\AnotherClassWithLogicInConstructor::__construct() contains code other than property assignments or assertions, which violates the "code-free constructor" principle (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::keepConstructorsCodeFree()->tips()[0],
                ),
                30,
            ],
        ]);
    }

    public function testValidConstructors(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/KeepConstructorsCodeFree/ClassesWithValidConstructors.php'], []);
    }

    public function testConstructorsWithAssertions(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/KeepConstructorsCodeFree/ClassWithAssertionLibrary.php'], []);
        $this->analyse([__DIR__ . '/../Fixtures/KeepConstructorsCodeFree/ClassWithMixedAssertionsAndAssignments.php'], []);
        $this->analyse([__DIR__ . '/../Fixtures/KeepConstructorsCodeFree/ClassWithNativeAssertions.php'], []);
        $this->analyse([__DIR__ . '/../Fixtures/KeepConstructorsCodeFree/ClassWithSelfAssertions.php'], []);
    }

    public function testConstructorWithParentCall(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/KeepConstructorsCodeFree/ClassWithParentConstructorCall.php'], []);
    }
}
