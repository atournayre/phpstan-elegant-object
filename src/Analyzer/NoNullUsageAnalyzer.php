<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\MethodExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PropertyExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\TestClassTrait;
use PhpParser\Node;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\UnionType;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;

/**
 * Classes do not use null: no nullable property, no null comparison, no null-safe operator.
 *
 * Complements NeverReturnNull and NeverAcceptNullArguments, which cover returns and parameters.
 * Promoted constructor parameters are parameters: they are left to NeverAcceptNullArguments.
 * Null coalescing (?? and ??=) is not flagged.
 */
final class NoNullUsageAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use PropertyExceptionTrait;
    use MethodExceptionTrait;
    use InterfaceExceptionTrait;
    use TestClassTrait;

    private const TIP = 'Use a Null Object or an exception instead of null (Elegant Object principle, chapter 4.1).';

    private bool $checkProperties;

    private bool $checkComparisons;

    private bool $checkNullsafe;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedPropertyNames
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedPropertyNames = [],
        array $allowedMethodNames = [],
        array $allowedInterfaces = [],
        bool $checkProperties = true,
        bool $checkComparisons = true,
        bool $checkNullsafe = true,
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedPropertiesNames = $allowedPropertyNames;
        $this->allowedMethodNames = $allowedMethodNames;
        $this->allowedInterfaces = $allowedInterfaces;
        $this->checkProperties = $checkProperties;
        $this->checkComparisons = $checkComparisons;
        $this->checkNullsafe = $checkNullsafe;
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$node instanceof InClassNode || !$node->getOriginalNode() instanceof Class_) { // @phpstan-ignore phpstanApi.instanceofAssumption
            return true;
        }

        $classReflection = $node->getClassReflection();
        if ($this->isTestClass($classReflection) || $this->isExcluded($scope->getFile())) {
            return true;
        }

        return $this->hasAllowedInterface($classReflection);
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

    /**
     * @return list<IdentifierRuleError>
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof InClassNode) { // @phpstan-ignore phpstanApi.instanceofAssumption
            return [];
        }

        $class = $node->getClassReflection()->getName();
        $errors = [];

        foreach ($node->getOriginalNode()->stmts as $statement) {
            if ($statement instanceof Property && $this->checkProperties) {
                array_push($errors, ...$this->propertyErrors($statement, $class));
            }

            if ($statement instanceof ClassMethod && !$this->isAllowedMethodName($statement->name->toString())) {
                array_push($errors, ...$this->methodErrors($statement, $class));
            }
        }

        return $errors;
    }

    /**
     * @return list<IdentifierRuleError>
     * @throws ShouldNotHappenException
     */
    private function propertyErrors(Property $property, string $class): array
    {
        $errors = [];

        foreach ($property->props as $item) {
            $name = $item->name->toString();
            if ($this->isAllowedPropertyName($name)) {
                continue;
            }

            if ($this->isNullableType($property->type)) {
                $errors[] = $this->error(
                    sprintf('Property %s::$%s is nullable.', $class, $name),
                    'elegantObject.nullUsage.nullableProperty',
                    $item->getStartLine(),
                );
            } elseif ($item->default !== null && $this->isNull($item->default)) {
                $errors[] = $this->error(
                    sprintf('Property %s::$%s defaults to null.', $class, $name),
                    'elegantObject.nullUsage.nullDefaultProperty',
                    $item->getStartLine(),
                );
            }
        }

        return $errors;
    }

    /**
     * @return list<IdentifierRuleError>
     * @throws ShouldNotHappenException
     */
    private function methodErrors(ClassMethod $method, string $class): array
    {
        $errors = [];
        $name = $method->name->toString();
        $finder = new NodeFinder();

        foreach ($finder->findInstanceOf($method->stmts ?? [], Expr::class) as $expression) {
            if ($this->checkComparisons && $this->isNullComparison($expression)) {
                $errors[] = $this->error(
                    sprintf('Method %s::%s() compares a value with null.', $class, $name),
                    'elegantObject.nullUsage.nullComparison',
                    $expression->getStartLine(),
                );
            }

            if ($this->checkNullsafe && $this->isNullsafe($expression)) {
                $errors[] = $this->error(
                    sprintf('Method %s::%s() uses the null-safe operator.', $class, $name),
                    'elegantObject.nullUsage.nullsafeOperator',
                    $expression->getStartLine(),
                );
            }
        }

        return $errors;
    }

    /**
     * @throws ShouldNotHappenException
     */
    private function error(string $message, string $identifier, int $line): IdentifierRuleError
    {
        return RuleErrorBuilder::message($message)
            ->identifier($identifier)
            ->line($line)
            ->tip(self::TIP)
            ->build();
    }

    private function isNullableType(Identifier|Name|ComplexType|null $type): bool
    {
        if ($type instanceof NullableType) {
            return true;
        }

        if ($type instanceof Identifier || $type instanceof Name) {
            return strtolower($type->toString()) === 'null';
        }

        if ($type instanceof UnionType) {
            foreach ($type->types as $member) {
                if ($this->isNullableType($member)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isNull(Expr $expression): bool
    {
        return $expression instanceof ConstFetch && $expression->name->toLowerString() === 'null';
    }

    private function isNullComparison(Expr $expression): bool
    {
        if ($expression instanceof Identical || $expression instanceof NotIdentical
            || $expression instanceof Equal || $expression instanceof NotEqual
        ) {
            return $this->isNull($expression->left) || $this->isNull($expression->right);
        }

        return $expression instanceof FuncCall
            && $expression->name instanceof Name
            && $expression->name->toLowerString() === 'is_null';
    }

    private function isNullsafe(Expr $expression): bool
    {
        return $expression instanceof NullsafePropertyFetch || $expression instanceof NullsafeMethodCall;
    }
}
