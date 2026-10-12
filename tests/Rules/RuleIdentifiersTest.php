<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Rules\AllPropertiesMustBePrivateRule;
use Atournayre\PHPStan\ElegantObject\Rules\AlwaysUseInterfaceRule;
use Atournayre\PHPStan\ElegantObject\Rules\BeEitherFinalOrAbstractRule;
use Atournayre\PHPStan\ElegantObject\Rules\BeImmutableRule;
use Atournayre\PHPStan\ElegantObject\Rules\DontUseStaticMethodsRule;
use Atournayre\PHPStan\ElegantObject\Rules\ExposeFewPublicMethodsRule;
use Atournayre\PHPStan\ElegantObject\Rules\KeepConstructorsCodeFreeRule;
use Atournayre\PHPStan\ElegantObject\Rules\KeepInterfacesShortRule;
use Atournayre\PHPStan\ElegantObject\Rules\NeverAcceptNullArgumentsRule;
use Atournayre\PHPStan\ElegantObject\Rules\NeverReturnNullRule;
use Atournayre\PHPStan\ElegantObject\Rules\NeverUseErNamesRule;
use Atournayre\PHPStan\ElegantObject\Rules\NeverUseGettersAndSettersRule;
use Atournayre\PHPStan\ElegantObject\Rules\NeverUsePublicConstantsRule;
use Atournayre\PHPStan\ElegantObject\Rules\NoNewOutsideSecondaryConstructorsRule;
use PhpParser\Node;
use PHPStan\Analyser\Error;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Identifiers must stay identical to the ones used by 1.x,
 * so that existing ignores by identifier keep working after upgrading.
 *
 * @extends RuleTestCase<Rule<covariant Node>>
 */
final class RuleIdentifiersTest extends RuleTestCase
{
    /** @var Rule<covariant Node> */
    private Rule $rule;

    protected function getRule(): Rule
    {
        return $this->rule;
    }

    /**
     * @param Rule<covariant Node> $rule
     * @param list<string> $expectedIdentifiers
     */
    #[DataProvider('rules')]
    public function testIdentifiers(Rule $rule, string $fixture, array $expectedIdentifiers): void
    {
        $this->rule = $rule;

        $identifiers = array_map(
            static fn (Error $error): ?string => $error->getIdentifier(),
            $this->gatherAnalyserErrors([__DIR__ . '/../Fixtures/' . $fixture]),
        );

        self::assertSame($expectedIdentifiers, array_values(array_unique($identifiers)));
    }

    /**
     * @return iterable<string, array{Rule<covariant Node>, string, list<string>}>
     */
    public static function rules(): iterable
    {
        yield 'AllPropertiesMustBePrivateRule' => [
            new AllPropertiesMustBePrivateRule(),
            'AllPropertiesMustBePrivate/PublicProperty.php',
            ['elegantObject.properties.notPrivate'],
        ];
        yield 'AlwaysUseInterfaceRule' => [
            new AlwaysUseInterfaceRule(),
            'AlwaysUseInterface/ClassesWithoutInterfaces.php',
            ['elegantObject.interfaces.noInterfaceImplemented'],
        ];
        yield 'BeEitherFinalOrAbstractRule' => [
            new BeEitherFinalOrAbstractRule(),
            'BeEitherFinalOrAbstract/SimpleClass.php',
            ['elegantObject.class.notFinalOrAbstract'],
        ];
        yield 'BeImmutableRule' => [
            new BeImmutableRule(),
            'BeImmutable/MutableClass.php',
            ['elegantObject.properties.mutableProperty', 'elegantObject.properties.propertyModified'],
        ];
        yield 'DontUseStaticMethodsRule' => [
            new DontUseStaticMethodsRule(),
            'DontUseStaticMethods/StaticMethods.php',
            ['elegantObject.staticMethods.staticMethodFound'],
        ];
        yield 'ExposeFewPublicMethodsRule' => [
            new ExposeFewPublicMethodsRule(),
            'ExposeFewPublicMethods/ClassWithTooManyPublicMethods.php',
            ['elegantObject.publicMethods.tooManyPublicMethods'],
        ];
        yield 'KeepConstructorsCodeFreeRule' => [
            new KeepConstructorsCodeFreeRule(),
            'KeepConstructorsCodeFree/ClassesWithConstructorCode.php',
            ['elegantObject.constructor.codeInConstructor'],
        ];
        yield 'KeepInterfacesShortRule' => [
            new KeepInterfacesShortRule(maxMethods: 3),
            'KeepInterfacesShort/LongInterface.php',
            ['elegantObject.interfaces.tooManyMethods'],
        ];
        yield 'NeverAcceptNullArgumentsRule (nullable)' => [
            new NeverAcceptNullArgumentsRule(),
            'NeverAcceptNullArguments/ClassWithNullableArguments.php',
            ['elegantObject.arguments.nullableParameter'],
        ];
        yield 'NeverAcceptNullArgumentsRule (null default)' => [
            new NeverAcceptNullArgumentsRule(),
            'NeverAcceptNullArguments/ClassWithNullDefaultArguments.php',
            ['elegantObject.arguments.nullDefaultValue'],
        ];
        yield 'NeverReturnNullRule' => [
            new NeverReturnNullRule(),
            'NeverReturnNull/ReturnNullClass.php',
            ['elegantObject.returnValue.nullReturned'],
        ];
        yield 'NeverUseErNamesRule' => [
            new NeverUseErNamesRule(),
            'NeverUseErNames/ErNamedClasses.php',
            ['elegantObject.naming.erSuffix'],
        ];
        yield 'NeverUseGettersAndSettersRule' => [
            new NeverUseGettersAndSettersRule(),
            'NeverUseGettersAndSetters/GettersSetters.php',
            ['elegantObject.methods.getter', 'elegantObject.methods.setter'],
        ];
        yield 'NeverUsePublicConstantsRule' => [
            new NeverUsePublicConstantsRule(),
            'NeverUsePublicConstants/PublicConstant.php',
            ['elegantObject.constants.publicConstant'],
        ];
        yield 'NoNewOutsideSecondaryConstructorsRule' => [
            new NoNewOutsideSecondaryConstructorsRule(),
            'NewOutsideSecondaryConstructors/InvalidClass.php',
            ['elegantObject.constructor.newOperatorUsed'],
        ];
    }
}
