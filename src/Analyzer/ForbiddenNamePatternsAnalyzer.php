<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

/**
 * Classes, interfaces, traits and enums must not carry a name that says nothing about what they are.
 *
 * Patterns are globs (`*` wildcard) matched case-sensitively against the short name.
 * The "-er"/"-or" suffixes (including Helper and Manager) are handled by NeverUseErNames and are not repeated here.
 */
final class ForbiddenNamePatternsAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use InterfaceExceptionTrait;

    public const DEFAULT_PATTERNS = ['Abstract*', 'Base*', '*Impl', '*Util', '*Utils', '*Data', '*Info'];

    /** @var array<string> */
    private array $forbiddenPatterns;

    /** @var array<string> */
    private array $allowedClasses;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $forbiddenPatterns Replaces the default patterns when not empty
     * @param array<string> $allowedClasses
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $forbiddenPatterns = [],
        array $allowedClasses = [],
        array $allowedInterfaces = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->forbiddenPatterns = $forbiddenPatterns === [] ? self::DEFAULT_PATTERNS : $forbiddenPatterns;
        $this->allowedClasses = $allowedClasses;
        $this->allowedInterfaces = $allowedInterfaces;
    }

    public function getNodeType(): string
    {
        return ClassLike::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof ClassLike || $node->name === null || $node->namespacedName === null) {
            return true;
        }

        if ($this->isExcludedPath($scope->getFile()) || $this->isTestName($node->name->toString())) {
            return true;
        }

        $fullName = $node->namespacedName->toString();
        if ($node instanceof Interface_) {
            return $this->isAllowedInterface($fullName);
        }

        return $this->isAllowedClass($fullName);
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof ClassLike || $node->name === null || $node->namespacedName === null) {
            return [];
        }

        $shortName = $node->name->toString();
        foreach ($this->forbiddenPatterns as $pattern) {
            if (fnmatch($pattern, $shortName, FNM_NOESCAPE)) {
                return RuleErrorFactory::createErrorWithTips(
                    'Name %s matches the forbidden pattern "%s". Pick a name that says what the object is.',
                    'elegantObject.naming.forbiddenPattern',
                    [$node->namespacedName->toString(), $pattern],
                )->errors();
            }
        }

        return [];
    }

    /**
     * Same convention as TestClassTrait: test doubles and in-memory fakes are not production names.
     */
    private function isTestName(string $shortName): bool
    {
        return str_contains($shortName, 'Test') || str_contains($shortName, 'InMemory');
    }

    private function isAllowedClass(string $className): bool
    {
        foreach ($this->allowedClasses as $allowed) {
            if (fnmatch($allowed, $className, FNM_NOESCAPE)) {
                return true;
            }
        }

        return false;
    }
}
