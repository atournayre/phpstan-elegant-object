<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\NoProtectedMembersRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoProtectedMembersRule<NodeAnalyzerInterface, Node>>
 */
class NoProtectedMembersRuleTest extends RuleTestCase
{
    private const NS = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoProtectedMembers\\';
    private const TIP = 'Prefer composition over inheritance: expose behavior through public interfaces.';

    /** @var array{excludedPaths?: array<string>, allowedMethodNames?: array<string>, allowedPropertyNames?: array<string>, allowedInterfaces?: array<string>, allowedClasses?: array<string>, allowInAbstractClasses?: bool} */
    private array $configuration = [];

    protected function getRule(): NoProtectedMembersRule
    {
        return new NoProtectedMembersRule(...$this->configuration);
    }

    private function fixture(string $class): string
    {
        return __DIR__ . '/../Fixtures/NoProtectedMembers/' . $class . '.php';
    }

    /**
     * @return array{string, int, string}
     */
    private function error(string $class, string $kind, string $name, int $line): array
    {
        return [
            sprintf(
                'Class %s%s declares protected %s %s. Protected is an inheritance hook: make it private.',
                self::NS,
                $class,
                $kind,
                $name,
            ),
            $line,
            self::TIP,
        ];
    }

    public function testFinalClassIsAlwaysFlagged(): void
    {
        $this->analyse([$this->fixture('FinalExample')], [
            $this->error('FinalExample', 'constant', 'LIMIT', 9),
            $this->error('FinalExample', 'property', '$name', 11),
            $this->error('FinalExample', 'property', '$promoted', 16),
            $this->error('FinalExample', 'method', 'helper()', 21),
        ]);
    }

    public function testAbstractClassFlagsEverythingButAbstractMethods(): void
    {
        $this->analyse([$this->fixture('Base')], [
            $this->error('Base', 'constant', 'SHARED', 9),
            $this->error('Base', 'property', '$shared', 11),
            $this->error('Base', 'property', '$extra', 13),
            $this->error('Base', 'method', 'concrete()', 17),
            $this->error('Base', 'method', 'overridable()', 21),
        ]);
    }

    public function testOverridingParentMembersIsNotFlagged(): void
    {
        $this->analyse([$this->fixture('Child')], [
            $this->error('Child', 'method', 'own()', 21),
        ]);
    }

    public function testConfiguredExemptions(): void
    {
        $this->configuration = [
            'allowedMethodNames' => ['allowedMethod'],
            'allowedPropertyNames' => ['allowedProperty'],
            'allowedClasses' => [self::NS . 'AllowedByNameClass'],
            'allowedInterfaces' => [self::NS . 'TolerantInterface'],
        ];

        $this->analyse([
            $this->fixture('Configurable'),
            $this->fixture('AllowedByNameClass'),
            $this->fixture('ImplementsTolerant'),
            $this->fixture('AbstractConfigurable'),
        ], [
            $this->error('Configurable', 'property', '$reported', 11),
            $this->error('Configurable', 'method', 'reportedMethod()', 17),
            $this->error('AbstractConfigurable', 'property', '$value', 9),
        ]);
    }

    public function testAbstractClassesCanBeAllowed(): void
    {
        $this->configuration = ['allowInAbstractClasses' => true];

        $this->analyse([$this->fixture('AbstractConfigurable'), $this->fixture('AllowedByNameClass')], [
            $this->error('AllowedByNameClass', 'property', '$value', 9),
        ]);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->configuration = ['excludedPaths' => [(string) realpath($this->fixture('ExcludedClass'))]];

        $this->analyse([$this->fixture('ExcludedClass')], []);
    }
}
