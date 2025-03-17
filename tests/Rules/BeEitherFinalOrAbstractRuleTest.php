<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Rules\BeEitherFinalOrAbstractRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<BeEitherFinalOrAbstractRule<NodeAnalyzerInterface, Node>>
 */
class BeEitherFinalOrAbstractRuleTest extends RuleTestCase
{
    protected function getRule(): BeEitherFinalOrAbstractRule
    {
        return new BeEitherFinalOrAbstractRule(
            excludedPaths: ['/excluded/path'],
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/BeEitherFinalOrAbstract/SimpleClass.php'], [
            [
                sprintf(
                    'Class Atournayre\PHPStan\ElegantObject\Tests\Fixtures\BeEitherFinalOrAbstract\SimpleClass is not final neither abstract, which violates the "be either final or abstract" principle (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::beEitherFinalOrAbstract()->tips()[0],
                ),
                6,
            ],
        ]);
    }

    public function testFinalClasses(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/BeEitherFinalOrAbstract/FinalClass.php'], []);
    }

    public function testAbstractClasses(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/BeEitherFinalOrAbstract/AbstractClass.php'], []);
    }

//    public function testExcludedPaths(): void
//    {
//        $this->analyse(['/excluded/path/test.php'], []);
//    }
}
