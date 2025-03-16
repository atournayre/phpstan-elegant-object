<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Rules\NoStaticMethodsRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoStaticMethodsRule<NodeAnalyzerInterface, Node>>
 */
final class NoStaticMethodsRuleTest extends RuleTestCase
{
    protected function getRule(): NoStaticMethodsRule
    {
        return new NoStaticMethodsRule(
            excludedPaths: ['/excluded/path'],
            allowedMethodNames: ['allowedStaticMethod'],
            allowedInterfaces: ['AllowedInterface', 'Namespace\*\AllowedInterface', '*Interface']
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoStaticMethods/StaticMethods.php'], [
            [
                sprintf(
                    'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoStaticMethods\StaticMethods::notAllowedStaticMethod() is static, which violates object encapsulation (Elegant Object principle). Only secondary constructors (factory methods) can be static.%s    💡 %s',
                    PHP_EOL,
                    TipFactory::staticMethods()->tips()[0],
                ),
                11,
            ],
        ]);
    }

    public function testNoErrorOnNonStaticMethod(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoStaticMethods/NonStaticMethod.php'], []);
    }

    public function testNoErrorOnAllowedStaticMethod(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoStaticMethods/AllowedStaticMethod.php'], []);
    }

    public function testNoErrorOnTestClass(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoStaticMethods/TestClass.php'], []);
    }

    public function testNoErrorOnInMemoryClass(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoStaticMethods/InMemoryClass.php'], []);
    }

    public function testNoErrorOnSecondaryConstructor(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoStaticMethods/SecondaryConstructor.php'], []);
    }

//    public function testNoErrorOnExcludedPath(): void
//    {
//        $this->analyse(['/excluded/path/SomeClass.php'], []);
//    }

    public function testNoErrorOnAllowedInterface(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoStaticMethods/Implementation.php'], []);
    }
}
