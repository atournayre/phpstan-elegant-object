<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PropertyExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\TestClassTrait;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Global_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Static_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\ShouldNotHappenException;

/**
 * No static or global state: static properties, static variables, `global` statements and `$GLOBALS`.
 * Complements DontUseStaticMethods, which only covers methods. Constants are not state and are never flagged.
 *
 * Exemptions are chosen by the developer: excluded paths, allowed names, allowed classes (wildcards)
 * and allowed interfaces (wildcards). Test classes are ignored.
 */
final class NoStaticPropertiesAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use PropertyExceptionTrait;
    use InterfaceExceptionTrait;
    use TestClassTrait;

    private const IDENTIFIER = 'elegantObject.staticState.found';

    /** @var array<string> */
    private array $allowedClasses;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedPropertiesNames
     * @param array<string> $allowedInterfaces
     * @param array<string> $allowedClasses
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedPropertiesNames = [],
        array $allowedInterfaces = [],
        array $allowedClasses = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedPropertiesNames = $allowedPropertiesNames;
        $this->allowedInterfaces = $allowedInterfaces;
        $this->allowedClasses = $allowedClasses;
    }

    public function getNodeType(): string
    {
        return Node::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        if (!$this->isStaticState($node)) {
            return true;
        }

        if ($this->isExcludedPath($scope->getFile())) {
            return true;
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return false;
        }

        return $this->isTestClass($classReflection)
            || $this->isAllowedClass($classReflection->getName())
            || $this->hasAllowedInterface($classReflection);
    }

    /**
     * @return list<IdentifierRuleError>
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        $context = $this->context($scope);
        $errors = [];

        if ($node instanceof Property) {
            foreach ($node->props as $property) {
                $name = $property->name->toString();
                if (!$this->isAllowedPropertyName($name)) {
                    $errors[] = $this->error(
                        'Static property %s::$%s is global state. Inject the state instead.',
                        [$context, $name],
                    );
                }
            }
        } elseif ($node instanceof Static_) {
            foreach ($node->vars as $variable) {
                $name = is_string($variable->var->name) ? $variable->var->name : 'unknown';
                if (!$this->isAllowedPropertyName($name)) {
                    $errors[] = $this->error(
                        'Static variable $%s in %s keeps hidden state between calls. Use an object property instead.',
                        [$name, $context],
                    );
                }
            }
        } elseif ($node instanceof Global_) {
            foreach ($node->vars as $variable) {
                $name = $variable instanceof Variable && is_string($variable->name) ? $variable->name : 'unknown';
                if (!$this->isAllowedPropertyName($name)) {
                    $errors[] = $this->error(
                        'Global variable $%s used in %s is global state. Inject it instead.',
                        [$name, $context],
                    );
                }
            }
        } else {
            $errors[] = $this->error('$GLOBALS used in %s is global state. Inject the data instead.', [$context]);
        }

        return $errors;
    }

    private function isStaticState(Node $node): bool
    {
        if ($node instanceof Property) {
            return $node->isStatic();
        }

        if ($node instanceof Variable) {
            return $node->name === 'GLOBALS';
        }

        return $node instanceof Static_ || $node instanceof Global_;
    }

    /**
     * @param array<int, string> $parameters
     * @throws ShouldNotHappenException
     */
    private function error(string $message, array $parameters): IdentifierRuleError
    {
        return RuleErrorFactory::createErrorWithTips($message, self::IDENTIFIER, $parameters)->errors()[0];
    }

    private function context(Scope $scope): string
    {
        $classReflection = $scope->getClassReflection();
        $function = $scope->getFunctionName();

        if ($classReflection === null) {
            return $function === null ? 'global scope' : $function . '()';
        }

        return $function === null
            ? $classReflection->getName()
            : $classReflection->getName() . '::' . $function . '()';
    }

    private function isAllowedClass(string $className): bool
    {
        foreach ($this->allowedClasses as $allowed) {
            $pattern = '/^' . str_replace('\*', '.*', preg_quote($allowed, '/')) . '$/';
            if (preg_match($pattern, $className) === 1) {
                return true;
            }
        }

        return false;
    }
}
