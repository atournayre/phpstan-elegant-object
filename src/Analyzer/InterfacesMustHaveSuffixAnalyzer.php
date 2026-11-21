<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use PhpParser\Node;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

/**
 * Interface names end with a configured suffix (default: "Interface").
 *
 * This is a project naming policy, not an Elegant Objects principle: Yegor dislikes "I" prefixes,
 * which is why the optional "forbidPrefix" flag also rejects names like "IFoo".
 *
 * Exemptions: excluded paths, excluded namespaces (wildcards), allowed names (wildcards, short or fully qualified).
 */
final class InterfacesMustHaveSuffixAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;

    /** @var array<string> */
    private array $excludedNamespaces;

    /** @var array<string> */
    private array $allowedClasses;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $excludedNamespaces
     * @param array<string> $allowedClasses
     */
    public function __construct(
        array $excludedPaths = [],
        array $excludedNamespaces = [],
        array $allowedClasses = [],
        private string $suffix = 'Interface',
        private bool $forbidPrefix = false,
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->excludedNamespaces = $excludedNamespaces;
        $this->allowedClasses = $allowedClasses;
    }

    public function getNodeType(): string
    {
        return Interface_::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof Interface_ || $node->name === null) {
            return true;
        }

        if ($this->isExcludedPath($scope->getFile())) {
            return true;
        }

        $shortName = $node->name->toString();
        $namespace = $scope->getNamespace() ?? '';
        $qualifiedName = $namespace === '' ? $shortName : $namespace . '\\' . $shortName;

        return $this->matchesAny($this->excludedNamespaces, $namespace)
            || $this->matchesAny($this->allowedClasses, $shortName)
            || $this->matchesAny($this->allowedClasses, $qualifiedName);
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof Interface_ || $node->name === null) {
            return [];
        }

        $shortName = $node->name->toString();
        $namespace = $scope->getNamespace() ?? '';
        $qualifiedName = $namespace === '' ? $shortName : $namespace . '\\' . $shortName;

        if (!str_ends_with($shortName, $this->suffix)) {
            return RuleErrorFactory::createErrorWithTips(
                'Interface %s must have the "%s" suffix.',
                'elegantObject.interfaces.missingSuffix',
                [$qualifiedName, $this->suffix],
            )->errors();
        }

        if ($this->forbidPrefix && preg_match('/^I[A-Z]/', $shortName) === 1) {
            return RuleErrorFactory::createErrorWithTips(
                'Interface %s must not use an "I" prefix.',
                'elegantObject.interfaces.forbiddenPrefix',
                [$qualifiedName],
            )->errors();
        }

        return [];
    }

    /**
     * @param array<string> $patterns
     */
    private function matchesAny(array $patterns, string $subject): bool
    {
        foreach ($patterns as $pattern) {
            $regex = '/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/';
            if (preg_match($regex, $subject) === 1) {
                return true;
            }
        }

        return false;
    }
}
