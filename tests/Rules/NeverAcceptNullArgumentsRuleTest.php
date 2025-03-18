<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Rules\NeverAcceptNullArgumentsRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NeverAcceptNullArgumentsRule<NodeAnalyzerInterface, Node>>
 */
class NeverAcceptNullArgumentsRuleTest extends RuleTestCase
{
    protected function getRule(): NeverAcceptNullArgumentsRule
    {
        return new NeverAcceptNullArgumentsRule(
            [],
            ['allowedMethod', 'anotherAllowedMethod'],
            ['allowedParameter', 'anotherAllowedParameter'],
            ['Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverAcceptNullArguments\AllowedInterface']
        );
    }

    public function testRule(): void
    {
        // Test pour les types nullables (paramètre de type ?Type)
        $this->analyse([__DIR__ . '/../Fixtures/NeverAcceptNullArguments/ClassWithNullableArguments.php'], [
            [
                sprintf(
                    'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverAcceptNullArguments\ClassWithNullableArguments::methodWithNullableArgument() accepts null for parameter $param, which violates the rule of never accepting null arguments (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::neverAcceptNullArguments()->tips()[0],
                ),
                9,
            ],
            [
                sprintf(
                    'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverAcceptNullArguments\ClassWithNullableArguments::methodWithMultipleParams() accepts null for parameter $nullableParam, which violates the rule of never accepting null arguments (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::neverAcceptNullArguments()->tips()[0],
                ),
                14,
            ],
        ]);

        // Test pour les paramètres avec valeur par défaut null
        $this->analyse([__DIR__ . '/../Fixtures/NeverAcceptNullArguments/ClassWithNullDefaultArguments.php'], [
            [
                sprintf(
                    'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverAcceptNullArguments\ClassWithNullDefaultArguments::methodWithNullDefault() has default value null for parameter $param, which violates the rule of never accepting null arguments (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::neverAcceptNullArguments()->tips()[0],
                ),
                9,
            ],
            [
                sprintf(
                    'Method Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverAcceptNullArguments\ClassWithNullDefaultArguments::methodWithMultipleParams() has default value null for parameter $paramWithNullDefault, which violates the rule of never accepting null arguments (Elegant Object principle).%s    💡 %s',
                    PHP_EOL,
                    TipFactory::neverAcceptNullArguments()->tips()[0],
                ),
                14,
            ],
        ]);

        // Test pour les paramètres autorisés
        $this->analyse([__DIR__ . '/../Fixtures/NeverAcceptNullArguments/ClassWithAllowedNullableArguments.php'], []);

        // Test pour les méthodes autorisées
        $this->analyse([__DIR__ . '/../Fixtures/NeverAcceptNullArguments/ClassWithAllowedMethodsAndParameters.php'], []);

        // Test pour les interfaces autorisées
        $this->analyse([__DIR__ . '/../Fixtures/NeverAcceptNullArguments/ClassImplementingAllowedInterface.php'], []);
    }
}
