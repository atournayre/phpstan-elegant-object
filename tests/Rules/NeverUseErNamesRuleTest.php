<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Rules\NeverUseErNamesRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NeverUseErNamesRule<NodeAnalyzerInterface, Node>>
 */
class NeverUseErNamesRuleTest extends RuleTestCase
{
    protected function getRule(): NeverUseErNamesRule
    {
        return new NeverUseErNamesRule(
            excludedPaths: ['/excluded/path'],
            excludedSuffixes: ['Converter']
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NeverUseErNames/ErNamedClasses.php'], [
            [
                sprintf(
                    'Class Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverUseErNames\UserManager has a name ending with "er", which violates object naming principles (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::neverUseErNames()->tips()[0],
                ),
                6,
            ],
            [
                sprintf(
                    'Class Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverUseErNames\DataFetcher has a name ending with "er", which violates object naming principles (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::neverUseErNames()->tips()[0],
                ),
                10,
            ],
        ]);
    }

    public function testExcludedSuffixes(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NeverUseErNames/ExcludedSuffixClass.php'], []);
    }

//    public function testExcludedPaths(): void
//    {
//        $this->analyse(['/excluded/path/test.php'], []);
//    }
}
