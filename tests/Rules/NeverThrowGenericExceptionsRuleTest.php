<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\NeverThrowGenericExceptionsRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NeverThrowGenericExceptionsRule<NodeAnalyzerInterface, Node>>
 */
class NeverThrowGenericExceptionsRuleTest extends RuleTestCase
{
    private const NS = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverThrowGenericExceptions\\';

    /** @var array{excludedPaths?: array<string>, allowedExceptions?: array<string>, allowedMethodNames?: array<string>, allowedClasses?: array<string>, requiredInterface?: string} */
    private array $configuration = [];

    protected function getRule(): NeverThrowGenericExceptionsRule
    {
        return new NeverThrowGenericExceptionsRule(...$this->configuration);
    }

    private function generic(string $class): string
    {
        return sprintf('Generic exception %s must not be thrown. Throw a specific domain exception instead.', $class);
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NeverThrowGenericExceptions/Example.php'], [
            [$this->generic('Exception'), 13],
            [$this->generic('InvalidArgumentException'), 18],
            [$this->generic('LogicException'), 23],
        ]);
    }

    public function testConfiguredExemptions(): void
    {
        $this->configuration = [
            'allowedExceptions' => ['DomainException', self::NS . 'Plain*'],
            'allowedMethodNames' => ['allowedMethod'],
        ];

        $this->analyse([__DIR__ . '/../Fixtures/NeverThrowGenericExceptions/Configurable.php'], [
            [$this->generic('Exception'), 11],
        ]);
    }

    public function testRequiredInterface(): void
    {
        $this->configuration = [
            'allowedMethodNames' => ['allowedMethod'],
            'allowedExceptions' => ['DomainException'],
            'requiredInterface' => self::NS . 'DomainFailure',
        ];

        $this->analyse([__DIR__ . '/../Fixtures/NeverThrowGenericExceptions/Configurable.php'], [
            [$this->generic('Exception'), 11],
            [sprintf('Exception %sPlainFailure must implement %sDomainFailure.', self::NS, self::NS), 26],
        ]);
    }

    public function testAllowedClassIsSkipped(): void
    {
        $this->configuration = ['allowedClasses' => [self::NS . 'AllowedClass']];

        $this->analyse([__DIR__ . '/../Fixtures/NeverThrowGenericExceptions/AllowedClass.php'], []);
    }

    public function testTestClassIsSkipped(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NeverThrowGenericExceptions/TestDouble.php'], []);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->configuration = ['excludedPaths' => [dirname(__DIR__) . '/Fixtures/NeverThrowGenericExceptions/ExcludedClass']];

        $this->analyse([__DIR__ . '/../Fixtures/NeverThrowGenericExceptions/ExcludedClass.php'], []);
    }
}
