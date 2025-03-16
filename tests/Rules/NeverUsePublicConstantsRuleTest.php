<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Rules\NeverUsePublicConstantsRule;
use Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverUsePublicConstants\AllowedClass;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NeverUsePublicConstantsRule<NodeAnalyzerInterface, Node>>
 */
class NeverUsePublicConstantsRuleTest extends RuleTestCase
{
    protected function getRule(): NeverUsePublicConstantsRule
    {
        return new NeverUsePublicConstantsRule(
            excludedPaths: [],
            allowedClasses: [AllowedClass::class],
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NeverUsePublicConstants/PublicConstant.php'], [
            [
                sprintf(
                    'Class Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverUsePublicConstants\PublicConstant has a public constant PUBLIC_CONSTANT, which violates encapsulation (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::neverUsePublicConstants()->tips()[0],
                ),
                9,
            ],
        ]);

        $this->analyse([__DIR__ . '/../Fixtures/NeverUsePublicConstants/ProtectedConstant.php'], []);
        $this->analyse([__DIR__ . '/../Fixtures/NeverUsePublicConstants/PrivateConstant.php'], []);
        $this->analyse([__DIR__ . '/../Fixtures/NeverUsePublicConstants/AllowedClass.php'], []);
    }
}