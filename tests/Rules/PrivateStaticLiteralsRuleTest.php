<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\PrivateStaticLiteralsRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<PrivateStaticLiteralsRule<NodeAnalyzerInterface, Node>>
 */
class PrivateStaticLiteralsRuleTest extends RuleTestCase
{
    private const NS = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\PrivateStaticLiterals\\';

    /** @var array{excludedPaths?: array<string>, allowedLiterals?: array<string>, minOccurrences?: int, minLength?: int} */
    private array $configuration = [];

    protected function getRule(): PrivateStaticLiteralsRule
    {
        return new PrivateStaticLiteralsRule(...$this->configuration);
    }

    private function message(string $class, string $literal, int $count): string
    {
        return sprintf(
            'Class %s%s repeats the literal \'%s\' %d times. Extract it to a private constant.',
            self::NS,
            $class,
            $literal,
            $count,
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/PrivateStaticLiterals/Example.php'], [
            [$this->message('Example', 'repeated', 2), 7],
            [$this->message('Example', 'twice', 4), 7],
            [$this->message('Example', 'key', 2), 7],
            [$this->message('Example', 'property', 2), 7],
        ]);
    }

    public function testConfiguration(): void
    {
        $this->configuration = ['allowedLiterals' => ['allowed'], 'minOccurrences' => 3, 'minLength' => 3];

        $this->analyse([__DIR__ . '/../Fixtures/PrivateStaticLiterals/Configurable.php'], [
            [$this->message('Configurable', 'thrice', 3), 7],
        ]);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->configuration = ['excludedPaths' => ['Fixtures/PrivateStaticLiterals/ExcludedClass']];

        $this->analyse([__DIR__ . '/../Fixtures/PrivateStaticLiterals/ExcludedClass.php'], []);
    }

    public function testTestClassIsSkipped(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/PrivateStaticLiterals/TestDouble.php'], []);
    }
}
