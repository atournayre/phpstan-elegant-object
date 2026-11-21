<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\InterfacesMustHaveSuffixRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<InterfacesMustHaveSuffixRule<NodeAnalyzerInterface, Node>>
 */
class InterfacesMustHaveSuffixRuleTest extends RuleTestCase
{
    private const NS = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\InterfacesMustHaveSuffix';

    /** @var array<string> */
    private array $excludedPaths = [];

    /** @var array<string> */
    private array $excludedNamespaces = [];

    /** @var array<string> */
    private array $allowedClasses = [];

    private string $suffix = 'Interface';

    private bool $forbidPrefix = false;

    protected function getRule(): InterfacesMustHaveSuffixRule
    {
        return new InterfacesMustHaveSuffixRule(
            $this->excludedPaths,
            $this->excludedNamespaces,
            $this->allowedClasses,
            $this->suffix,
            $this->forbidPrefix,
        );
    }

    private function missing(string $name, string $suffix = 'Interface'): string
    {
        return sprintf('Interface %s\%s must have the "%s" suffix.', self::NS, $name, $suffix);
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/InterfacesMustHaveSuffix/Example.php'], [
            [$this->missing('Reader'), 7],
            [$this->missing('Legacy'), 19],
        ]);
    }

    public function testCustomSuffix(): void
    {
        $this->suffix = 'Contract';

        $this->analyse([__DIR__ . '/../Fixtures/InterfacesMustHaveSuffix/Example.php'], [
            [$this->missing('Reader', 'Contract'), 7],
            [$this->missing('WriterInterface', 'Contract'), 11],
            [$this->missing('IReaderInterface', 'Contract'), 15],
            [$this->missing('Legacy', 'Contract'), 19],
        ]);
    }

    public function testAllowedNames(): void
    {
        $this->allowedClasses = ['Reader', self::NS . '\Leg*'];

        $this->analyse([__DIR__ . '/../Fixtures/InterfacesMustHaveSuffix/Example.php'], []);
    }

    public function testForbidPrefix(): void
    {
        $this->forbidPrefix = true;
        $this->allowedClasses = ['Reader', 'Legacy'];

        $this->analyse([__DIR__ . '/../Fixtures/InterfacesMustHaveSuffix/Example.php'], [
            [sprintf('Interface %s\IReaderInterface must not use an "I" prefix.', self::NS), 15],
        ]);
    }

    public function testExcludedNamespaceIsSkipped(): void
    {
        $this->excludedNamespaces = [self::NS . '\Skipped'];

        $this->analyse([__DIR__ . '/../Fixtures/InterfacesMustHaveSuffix/Skipped/Ignored.php'], []);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->excludedPaths = [dirname(__DIR__) . '/Fixtures/InterfacesMustHaveSuffix/Skipped'];

        $this->analyse([__DIR__ . '/../Fixtures/InterfacesMustHaveSuffix/Skipped/Ignored.php'], []);
    }
}
