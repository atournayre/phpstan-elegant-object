<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\ConstructorsMustBePrivateRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ConstructorsMustBePrivateRule<NodeAnalyzerInterface, Node>>
 */
class ConstructorsMustBePrivateRuleTest extends RuleTestCase
{
    private const FIXTURES_NAMESPACE = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ConstructorsMustBePrivate';

    protected function getRule(): ConstructorsMustBePrivateRule
    {
        return new ConstructorsMustBePrivateRule(
            excludedPaths: [dirname(__DIR__) . '/Fixtures/ConstructorsMustBePrivate/ExcludedPath'],
            excludedNamespaces: [self::FIXTURES_NAMESPACE . '\ExcludedNamespace'],
            allowedInterfaces: [self::FIXTURES_NAMESPACE . '\FrameworkInterface'],
        );
    }

    public function testNonPrivateConstructorsAreReported(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/ConstructorsMustBePrivate/ClassesWithConstructors.php'], [
            [$this->message('PublicConstructor', 'public'), 9],
            [$this->message('ImplicitPublicConstructor', 'public'), 16],
            [$this->message('ProtectedConstructor', 'protected'), 23],
            [$this->message('PromotedPublicConstructor', 'public'), 30],
        ]);
    }

    public function testConstructorsRequiredByContractsAreAllowed(): void
    {
        $this->analyse([
            __DIR__ . '/../Fixtures/ConstructorsMustBePrivate/ImplementsInterfaceDeclaringConstructor.php',
            __DIR__ . '/../Fixtures/ConstructorsMustBePrivate/ExtendsAbstractDeclaringConstructor.php',
        ], []);
    }

    public function testConstructorNotRequiredByContractIsReported(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/ConstructorsMustBePrivate/ExtendsConcreteConstructor.php'], [
            [$this->message('ExtendsConcreteConstructor', 'public'), 9],
        ]);
    }

    public function testAllowedInterfaceIsSkipped(): void
    {
        $this->analyse([
            __DIR__ . '/../Fixtures/ConstructorsMustBePrivate/ImplementsAllowedInterface.php',
            __DIR__ . '/../Fixtures/ConstructorsMustBePrivate/ExtendsClassImplementingAllowedInterface.php',
        ], []);
    }

    public function testExcludedNamespaceIsSkipped(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/ConstructorsMustBePrivate/ExcludedNamespace/ExcludedNamespaceClass.php'], []);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/ConstructorsMustBePrivate/ExcludedPath/ExcludedPathClass.php'], []);
    }

    private function message(string $class, string $visibility): string
    {
        return sprintf(
            'Constructor %s\%s::__construct() must be private (found %s). Use a named constructor (static factory) instead.',
            self::FIXTURES_NAMESPACE,
            $class,
            $visibility,
        );
    }
}
