<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\NoPrimitiveReturnRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoPrimitiveReturnRule<NodeAnalyzerInterface, Node>>
 */
class NoPrimitiveReturnRuleTest extends RuleTestCase
{
    private const NS = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoPrimitiveReturn\\';

    /** @var array<string, array<string>> */
    private array $configuration = [];

    protected function getRule(): NoPrimitiveReturnRule
    {
        return new NoPrimitiveReturnRule(...$this->configuration);
    }

    private function message(string $class, string $method): string
    {
        return sprintf(
            'Method %s%s::%s() returns a primitive type. Return a value object instead.',
            self::NS,
            $class,
            $method,
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/NoPrimitiveReturn/Example.php'], [
            [$this->message('Example', 'plain'), 13],
            [$this->message('Example', 'nullable'), 18],
            [$this->message('Example', 'union'), 23],
            [$this->message('Example', 'untyped'), 28],
            [$this->message('Example', 'listing'), 33],
            [$this->message('Example', 'predicate'), 38],
            [$this->message('Example', '__invoke'), 92],
            [$this->message('Contract', 'impose'), 127],
            [$this->message('Base', 'template'), 140],
        ]);
    }

    public function testConfiguredExemptions(): void
    {
        $this->configuration = [
            'allowedMethodNames' => ['allowedMethod'],
            'allowedTypes' => ['bool'],
            'allowedClasses' => [self::NS . 'AllowedBy*Class'],
            'allowedInterfaces' => [self::NS . 'TolerantInterface'],
        ];

        $this->analyse([__DIR__ . '/../Fixtures/NoPrimitiveReturn/Configurable.php'], [
            [$this->message('Configurable', 'stillReported'), 19],
        ]);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->configuration = ['excludedPaths' => ['Fixtures/NoPrimitiveReturn/ExcludedClass']];

        $this->analyse([__DIR__ . '/../Fixtures/NoPrimitiveReturn/ExcludedClass.php'], []);
    }
}
