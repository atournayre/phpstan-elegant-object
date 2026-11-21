<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\ConstructorObjectsOnlyRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ConstructorObjectsOnlyRule<NodeAnalyzerInterface, Node>>
 */
class ConstructorObjectsOnlyRuleTest extends RuleTestCase
{
    private const NS = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ConstructorObjectsOnly\\';

    /** @var array<string, array<string>> */
    private array $configuration = [];

    private bool $checkPrivateConstructors = false;

    protected function getRule(): ConstructorObjectsOnlyRule
    {
        return new ConstructorObjectsOnlyRule(
            ...$this->configuration,
            checkPrivateConstructors: $this->checkPrivateConstructors,
        );
    }

    private function message(string $class, string $parameters): string
    {
        return sprintf(
            'Constructor of %s%s accepts non-object parameters (%s). '
            . 'Pass objects, and build them in a secondary constructor.',
            self::NS,
            $class,
            $parameters,
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/ConstructorObjectsOnly/Example.php'], [
            [$this->message('Example', '$name'), 9],
            [$this->message('Nullable', '$number'), 16],
            [$this->message('Union', '$id'), 23],
            [$this->message('Untyped', '$value'), 30],
            [$this->message('Several', '$items, $others, $any, $flag, $ratio'), 37],
        ]);
    }

    public function testPrivateConstructorsCanBeChecked(): void
    {
        $this->checkPrivateConstructors = true;

        $this->analyse([__DIR__ . '/../Fixtures/ConstructorObjectsOnly/Example.php'], [
            [$this->message('Example', '$name'), 9],
            [$this->message('Nullable', '$number'), 16],
            [$this->message('Union', '$id'), 23],
            [$this->message('Untyped', '$value'), 30],
            [$this->message('Several', '$items, $others, $any, $flag, $ratio'), 37],
            [$this->message('PrivateConstructor', '$value'), 58],
        ]);
    }

    public function testConfiguredExemptions(): void
    {
        $this->configuration = [
            'allowedParameterNames' => ['skipped'],
            'allowedTypes' => ['string'],
            'allowedClasses' => [self::NS . 'AllowedBy*Class'],
            'allowedInterfaces' => [self::NS . 'TolerantInterface'],
        ];

        $this->analyse([__DIR__ . '/../Fixtures/ConstructorObjectsOnly/Configurable.php'], [
            [$this->message('Configurable', '$reported'), 9],
            [$this->message('TypeAllowed', '$number'), 16],
        ]);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->configuration = ['excludedPaths' => ['Fixtures/ConstructorObjectsOnly/ExcludedClass']];

        $this->analyse([__DIR__ . '/../Fixtures/ConstructorObjectsOnly/ExcludedClass.php'], []);
    }
}
