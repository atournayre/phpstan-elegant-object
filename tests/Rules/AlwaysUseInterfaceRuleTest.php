<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Rules\AlwaysUseInterfaceRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<AlwaysUseInterfaceRule<NodeAnalyzerInterface, Node>>
 */
class AlwaysUseInterfaceRuleTest extends RuleTestCase
{
    protected function getRule(): AlwaysUseInterfaceRule
    {
        return new AlwaysUseInterfaceRule(
            excludedPaths: ['/excluded/path'],
            excludedClasses: ['Atournayre\PHPStan\ElegantObject\Tests\Fixtures\AlwaysUseInterface\ExcludedClass']
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/AlwaysUseInterface/ClassesWithoutInterfaces.php'], [
            [
                sprintf(
                    'Class Atournayre\PHPStan\ElegantObject\Tests\Fixtures\AlwaysUseInterface\ClassWithoutInterface does not implement any interfaces, which violates the "always use interface" principle (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::alwaysUseInterface()->tips()[0],
                ),
                6,
            ],
            [
                sprintf(
                    'Class Atournayre\PHPStan\ElegantObject\Tests\Fixtures\AlwaysUseInterface\AnotherClassWithoutInterface does not implement any interfaces, which violates the "always use interface" principle (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::alwaysUseInterface()->tips()[0],
                ),
                10,
            ],
        ]);
    }

    public function testClassesWithInterfaces(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/AlwaysUseInterface/ClassesWithInterfaces.php'], []);
    }

    public function testExcludedClasses(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/AlwaysUseInterface/ExcludedClasses.php'], []);
    }

    public function testAbstractClasses(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/AlwaysUseInterface/AbstractClasses.php'], []);
    }

//    public function testExcludedPaths(): void
//    {
//        $this->analyse(['/excluded/path/test.php'], []);
//    }
}
