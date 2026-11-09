<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\NoBooleanFlagArgumentRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoBooleanFlagArgumentRule<NodeAnalyzerInterface, Node>>
 */
class NoBooleanFlagArgumentRuleTest extends RuleTestCase
{
    protected function getRule(): NoBooleanFlagArgumentRule
    {
        return new NoBooleanFlagArgumentRule(['Fixtures/NoBooleanFlagArgument/ExcludedClass']);
    }

    public function testRule(): void
    {
        $message = 'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoBooleanFlagArgument\ClassWithBooleanFlags::%s() defines boolean flag parameter $flag. Split the behavior into explicit methods or use a dedicated value object/enum.';

        $this->analyse([__DIR__ . '/../Fixtures/NoBooleanFlagArgument/ClassWithBooleanFlags.php'], [
            [sprintf($message, 'plain'), 9],
            [sprintf($message, 'nullable'), 13],
            [sprintf($message, 'union'), 17],
        ]);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoBooleanFlagArgument/ExcludedClass.php'], []);
    }
}
