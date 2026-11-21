<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\ExceptionsMustUseNamedConstructorsRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ExceptionsMustUseNamedConstructorsRule<NodeAnalyzerInterface, Node>>
 */
class ExceptionsMustUseNamedConstructorsRuleTest extends RuleTestCase
{
    private const NS = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ExceptionsMustUseNamedConstructors\\';

    /** @var array<string, array<string>> */
    private array $configuration = [];

    protected function getRule(): ExceptionsMustUseNamedConstructorsRule
    {
        return new ExceptionsMustUseNamedConstructorsRule(...$this->configuration);
    }

    private function newMessage(string $class): string
    {
        return sprintf(
            'Exception %s is built with the new operator. Use a named constructor instead (e.g. %s::because...()).',
            $class,
            $class,
        );
    }

    private function constructorMessage(string $class): string
    {
        return sprintf(
            'Exception %s%s must not expose a constructor. Make it private and expose named constructors.',
            self::NS,
            $class,
        );
    }

    private function fixture(string $name): string
    {
        return __DIR__ . '/../Fixtures/ExceptionsMustUseNamedConstructors/' . $name . '.php';
    }

    public function testNewOperator(): void
    {
        $this->analyse([$this->fixture('Thrower')], [
            [$this->newMessage(self::NS . 'UserInvalid'), 16],
            [$this->newMessage(self::NS . 'LooseException'), 26],
            [$this->newMessage(self::NS . 'AllowedByNameException'), 36],
            [$this->newMessage(self::NS . 'AllowedByInterfaceException'), 37],
        ]);
    }

    public function testNonPrivateConstructor(): void
    {
        $this->analyse([$this->fixture('LooseException')], [
            [$this->constructorMessage('LooseException'), 9],
        ]);
    }

    public function testNamedConstructorsAreAllowedInsideTheExceptionItself(): void
    {
        $this->analyse([$this->fixture('UserInvalid')], []);
    }

    public function testConfiguredExemptions(): void
    {
        $this->configuration = [
            'allowedClasses' => [self::NS . 'AllowedByName*'],
            'allowedInterfaces' => [self::NS . 'TolerantInterface'],
        ];

        $this->analyse([$this->fixture('Thrower'), $this->fixture('AllowedByNameException')], [
            [$this->newMessage(self::NS . 'UserInvalid'), 16],
            [$this->newMessage(self::NS . 'LooseException'), 26],
        ]);
    }

    public function testExcludedNamespaceIsSkipped(): void
    {
        $this->configuration = ['excludedNamespaces' => [self::NS]];

        $this->analyse([$this->fixture('Thrower'), $this->fixture('LooseException')], [
        ]);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->configuration = ['excludedPaths' => [dirname(__DIR__) . '/Fixtures/ExceptionsMustUseNamedConstructors']];

        $this->analyse([$this->fixture('Thrower'), $this->fixture('LooseException')], []);
    }

    public function testTestClassesAreSkipped(): void
    {
        $this->analyse([$this->fixture('TestedThrower')], []);
    }
}
