<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Analyzer;

use Atournayre\PHPStan\ElegantObject\Factory\RuleErrorFactory;
use Atournayre\PHPStan\ElegantObject\Factory\TipFactory;
use Atournayre\PHPStan\ElegantObject\Traits\InterfaceExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\PathExclusionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\MethodExceptionTrait;
use Atournayre\PHPStan\ElegantObject\Traits\ParameterExceptionTrait;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\NullableType;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;

final class NeverAcceptNullArgumentsAnalyzer extends RuleAnalyzer
{
    use PathExclusionTrait;
    use MethodExceptionTrait;
    use InterfaceExceptionTrait;
    use ParameterExceptionTrait;

    /**
     * @param array<string> $excludedPaths
     * @param array<string> $allowedMethodNames
     * @param array<string> $allowedParameterNames
     * @param array<string> $allowedInterfaces
     */
    public function __construct(
        array $excludedPaths = [],
        array $allowedMethodNames = [],
        array $allowedParameterNames = [],
        array $allowedInterfaces = [],
    ) {
        $this->excludedPaths = $excludedPaths;
        $this->allowedMethodNames = $allowedMethodNames;
        $this->allowedParameterNames = $allowedParameterNames;
        $this->allowedInterfaces = $allowedInterfaces;
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function shouldSkipAnalysis(Node $node, Scope $scope): bool
    {
        // Vérifier le type de nœud correct
        if (!$node instanceof ClassMethod) {
            return true;
        }

        // Ignorer si la méthode n'a pas de paramètres
        if (empty($node->getParams())) {
            return true;
        }

        // Ignorer si le chemin est exclu
        if ($this->isExcludedPath($scope->getFile())) {
            return true;
        }

        $methodName = $node->name->toString();

        // Ignorer si la méthode est autorisée
        if ($this->isAllowedMethodName($methodName)) {
            return true;
        }

        $classReflection = $scope->getClassReflection();

        // Continuer l'analyse si aucune classe n'est disponible
        if ($classReflection === null) {
            return false;
        }

        // Ignorer si la classe implémente une interface autorisée
        if ($this->hasAllowedInterface($classReflection)) {
            return true;
        }

        // Ignorer si la méthode provient d'une interface autorisée
        if ($this->isMethodFromAllowedInterface($classReflection, $methodName)) {
            return true;
        }

        // Analyser dans tous les autres cas
        return false;
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function analyze(Node $node, Scope $scope): array
    {
        if (!$node instanceof ClassMethod) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (null === $classReflection) {
            return [];
        }

        $errors = [];
        $className = $classReflection->getName();
        $methodName = $node->name->toString();

        foreach ($node->getParams() as $param) {
            if (!$param->var instanceof Node\Expr\Variable) {
                continue;
            }

            // The name property in Variable node can be either string or Expr
            if (!is_string($param->var->name)) {
                continue;
            }

            $paramName = $param->var->name;

            if ($this->isAllowedParameterName($paramName)) {
                continue;
            }

            // Check if parameter has nullable type hint
            if ($param->type instanceof NullableType) {
                $errors[] = RuleErrorFactory::createErrorWithTips(
                    'Method %s::%s() accepts null for parameter $%s, which violates the rule of never accepting null arguments (Elegant Object principle).',
                    'elegantObject.arguments.nullableParameter',
                    [$className, $methodName, $paramName],
                    TipFactory::neverAcceptNullArguments()->tips(),
                )->errors()[0];
            }

            // Check if parameter has default value set to null
            if ($param->default instanceof Node\Expr\ConstFetch) {
                if ($param->default->name->toString() === 'null') {
                    $errors[] = RuleErrorFactory::createErrorWithTips(
                        'Method %s::%s() has default value null for parameter $%s, which violates the rule of never accepting null arguments (Elegant Object principle).',
                        'elegantObject.arguments.nullDefaultValue',
                        [$className, $methodName, $paramName],
                        TipFactory::neverAcceptNullArguments()->tips(),
                    )->errors()[0];
                }
            }
        }

        return $errors;
    }
}
