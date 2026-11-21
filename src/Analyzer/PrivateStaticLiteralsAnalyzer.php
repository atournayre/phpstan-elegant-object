<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\TestClassTrait;
use PhpParser\Node;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\ShouldNotHappenException;

/**
 * A string literal repeated in the method bodies of a class is extracted to a private constant.
 *
 * Interpretation of "private static literals": a literal used several times is a hidden shared concept,
 * so it gets a name. Only string literals are considered. Ignored: literals shorter than `minLength`,
 * array keys in array literals, nested (anonymous) classes, and everything outside method bodies
 * (constants, properties, attributes, parameter defaults). Test classes are skipped.
 */
final class PrivateStaticLiteralsAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use TestClassTrait;

    private const ALWAYS_IGNORED = ['', '0', '1'];

    /** @var array<string> */
    private array $allowedLiterals;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedLiterals
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedLiterals = [],
        private int $minOccurrences = 2,
        private int $minLength = 2,
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedLiterals = $allowedLiterals;
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof InClassNode) { // @phpstan-ignore phpstanApi.instanceofAssumption
            return true;
        }

        if ($this->isExcluded($scope->getFile())) {
            return true;
        }

        $classReflection = $node->getClassReflection();

        return $classReflection->isAnonymous() || $this->isTestClass($classReflection);
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof InClassNode) { // @phpstan-ignore phpstanApi.instanceofAssumption
            return [];
        }

        $counts = [];
        foreach ($node->getOriginalNode()->getMethods() as $method) {
            $this->collect($method, $counts);
        }

        $errors = [];
        foreach ($counts as $literal => $count) {
            $literal = (string) $literal;
            if ($count < $this->minOccurrences || !$this->isCandidate($literal)) {
                continue;
            }

            $errors = array_merge($errors, RuleErrorFactory::createErrorWithTips(
                'Class %s repeats the literal \'%s\' %d times. Extract it to a private constant.',
                'elegantObject.literals.repeated',
                [$node->getClassReflection()->getName(), $literal, $count],
            )->errors());
        }

        return $errors;
    }

    /**
     * @param array<int|string, int> $counts
     */
    private function collect(Node $node, array &$counts): void
    {
        foreach ($node->getSubNodeNames() as $name) {
            $child = $node->{$name};
            foreach (is_array($child) ? $child : [$child] as $item) {
                if ($item instanceof Node) {
                    $this->visit($item, $counts);
                }
            }
        }
    }

    /**
     * @param array<int|string, int> $counts
     */
    private function visit(Node $node, array &$counts): void
    {
        if ($node instanceof ClassLike || $node instanceof AttributeGroup || $node instanceof Param) {
            return;
        }

        if ($node instanceof String_) {
            $counts[$node->value] = ($counts[$node->value] ?? 0) + 1;

            return;
        }

        if ($node instanceof ArrayItem && $node->key instanceof String_) {
            $this->visit($node->value, $counts);

            return;
        }

        $this->collect($node, $counts);
    }

    private function isCandidate(string $literal): bool
    {
        return !in_array($literal, self::ALWAYS_IGNORED, true)
            && !in_array($literal, $this->allowedLiterals, true)
            && mb_strlen($literal) >= $this->minLength;
    }

    private function isExcluded(string $file): bool
    {
        if ($this->isExcludedPath($file)) {
            return true;
        }

        foreach ($this->excludedPaths as $excludedPath) {
            if ($excludedPath !== '' && str_contains($file, $excludedPath)) {
                return true;
            }
        }

        return false;
    }
}
