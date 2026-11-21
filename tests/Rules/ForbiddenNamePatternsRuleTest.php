<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Rules;

use Atournayre\PHPStan\ElegantObject\Contract\NodeAnalyzerInterface;
use Atournayre\PHPStan\ElegantObject\Rules\ForbiddenNamePatternsRule;
use PhpParser\Node;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ForbiddenNamePatternsRule<NodeAnalyzerInterface, Node>>
 */
class ForbiddenNamePatternsRuleTest extends RuleTestCase
{
    private const NS = 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ForbiddenNamePatterns\\';

    /** @var array<string, array<string>> */
    private array $configuration = [];

    protected function getRule(): ForbiddenNamePatternsRule
    {
        return new ForbiddenNamePatternsRule(...$this->configuration);
    }

    private function message(string $name, string $pattern): string
    {
        return sprintf(
            'Name %s%s matches the forbidden pattern "%s". Pick a name that says what the object is.',
            self::NS,
            $name,
            $pattern,
        );
    }

    public function testDefaultPatterns(): void
    {
        $this->analyse([__DIR__ . '/../Fixtures/ForbiddenNamePatterns/Example.php'], [
            [$this->message('AbstractShape', 'Abstract*'), 7],
            [$this->message('BaseShape', 'Base*'), 11],
            [$this->message('ShapeImpl', '*Impl'), 15],
            [$this->message('StringUtil', '*Util'), 19],
            [$this->message('StringUtils', '*Utils'), 23],
            [$this->message('UserData', '*Data'), 27],
            [$this->message('UserInfo', '*Info'), 31],
            [$this->message('ShapeInfo', '*Info'), 35],
            [$this->message('BaseBehaviour', 'Base*'), 39],
            [$this->message('ColorData', '*Data'), 43],
        ]);
    }

    public function testCustomPatternsReplaceDefaults(): void
    {
        $this->configuration = ['forbiddenPatterns' => ['*Dto']];

        $this->analyse([__DIR__ . '/../Fixtures/ForbiddenNamePatterns/Configurable.php'], [
            [$this->message('ReportDto', '*Dto'), 7],
            [$this->message('AllowedDto', '*Dto'), 11],
            [$this->message('ShapeDto', '*Dto'), 15],
            [$this->message('TolerantDto', '*Dto'), 19],
        ]);
    }

    public function testAllowedClassesAndInterfaces(): void
    {
        $this->configuration = [
            'forbiddenPatterns' => ['*Dto'],
            'allowedClasses' => [self::NS . 'Allowed*'],
            'allowedInterfaces' => [self::NS . 'Tolerant*'],
        ];

        $this->analyse([__DIR__ . '/../Fixtures/ForbiddenNamePatterns/Configurable.php'], [
            [$this->message('ReportDto', '*Dto'), 7],
            [$this->message('ShapeDto', '*Dto'), 15],
        ]);
    }

    public function testExcludedPathIsSkipped(): void
    {
        $this->configuration = ['excludedPaths' => [(string) realpath(__DIR__ . '/../Fixtures/ForbiddenNamePatterns')]];

        $this->analyse([__DIR__ . '/../Fixtures/ForbiddenNamePatterns/ExcludedClass.php'], []);
    }
}
