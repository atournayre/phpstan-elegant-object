<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Rules\DontUseStaticMethodsRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<DontUseStaticMethodsRule<NodeAnalyzerInterface, Node>>
 */
final class DontUseStaticMethodsRuleTest extends RuleTestCase
{
    protected function getRule(): DontUseStaticMethodsRule
    {
        return new DontUseStaticMethodsRule(
            excludedPaths: ['/excluded/path'],
            allowedMethodNames: ['allowedStaticMethod'],
            allowedInterfaces: ['AllowedInterface', 'Namespace\*\AllowedInterface', '*Interface']
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/DontUseStaticMethods/StaticMethods.php'], [
            [
                sprintf(
                    'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\DontUseStaticMethods\StaticMethods::notAllowedStaticMethod() is static, which violates object encapsulation (Elegant Object principle). Only secondary constructors (factory methods) can be static.%s    💡 %s',
                    PHP_EOL,
                    TipFactory::staticMethods()->tips()[0],
                ),
                11,
            ],
        ]);
    }

    public function testNoErrorOnNonStaticMethod(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/DontUseStaticMethods/NonStaticMethod.php'], []);
    }

    public function testNoErrorOnAllowedStaticMethod(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/DontUseStaticMethods/AllowedStaticMethod.php'], []);
    }

    public function testNoErrorOnTestClass(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/DontUseStaticMethods/TestClass.php'], []);
    }

    public function testNoErrorOnInMemoryClass(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/DontUseStaticMethods/InMemoryClass.php'], []);
    }

    public function testNoErrorOnSecondaryConstructor(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/DontUseStaticMethods/SecondaryConstructor.php'], []);
    }

//    public function testNoErrorOnExcludedPath(): void
//    {
//        $this->analyse(['/excluded/path/SomeClass.php'], []);
//    }

    public function testNoErrorOnAllowedInterface(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/DontUseStaticMethods/Implementation.php'], []);
    }
}
