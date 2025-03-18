# Configuration examples

```yaml
# AllPropertiesMustBePrivateRule
-
  class: Atournayre\PHPStan\ElegantObject\Rules\AllPropertiesMustBePrivateRule
  arguments:
    excludedPaths:
      - 'path/to/exclude/*'
      - 'another/path/to/exclude/*.php'
    allowedPropertiesNames:
      - 'propertyName1'
      - 'propertyName2'
    allowedInterfaces:
      - 'App\Interface1'
      - 'App\Interface2'
  tags:
    - phpstan.rules.rule

# AlwaysUseInterfaceRule
-
  class: Atournayre\PHPStan\ElegantObject\Rules\AlwaysUseInterfaceRule
  arguments:
    excludedPaths:
      - '/excluded/path'
      - '/excluded/path/*.php'
    excludedClasses:
      - 'Atournayre\PHPStan\ElegantObject\Tests\Fixtures\AlwaysUseInterface\ExcludedClass'
      - 'App\ExcludedClass'
  tags:
    - phpstan.rules.rule

# BeEitherFinalOrAbstractRule
-
  class: Atournayre\PHPStan\ElegantObject\Rules\BeEitherFinalOrAbstractRule
  arguments:
    excludedPaths:
      - '/excluded/path'
      - '/excluded/path/*.php'
  tags:
    - phpstan.rules.rule

# BeImmutableRule
-
  class: Atournayre\PHPStan\ElegantObject\Rules\BeImmutableRule
  arguments:
    excludedPaths:
      - '/excluded/path'
      - '/excluded/path/*.php'
    allowedPropertyNames:
      - 'cache'
      - 'logger'
    allowedInterfaces:
      - 'App\Contract\MutableInterface'
    allowedMethodNames:
      - 'setUp'
      - 'init'
  tags:
    - phpstan.rules.rule

# DontUseStaticMethodsRule
-
  class: Atournayre\PHPStan\ElegantObject\Rules\DontUseStaticMethodsRule
  arguments:
    excludedPaths:
      - '/excluded/path'
      - '/excluded/path/*.php'
    allowedMethodNames:
      - 'getInstance'
      - 'create'
    allowedInterfaces:
      - 'App\Factory\FactoryInterface'
  tags:
    - phpstan.rules.rule

# KeepConstructorsCodeFreeRule
-
  class: Atournayre\PHPStan\ElegantObject\Rules\KeepConstructorsCodeFreeRule
  arguments:
    excludedPaths:
      - '/excluded/path'
      - '/excluded/path/*.php'
  tags:
    - phpstan.rules.rule

# NeverReturnNullRule
-
  class: Atournayre\PHPStan\ElegantObject\Rules\NeverReturnNullRule
  arguments:
    excludedPaths:
      - '/excluded/path'
      - '/excluded/path/*.php'
    allowedMethodNames:
      - 'findOneBy'
      - 'getParameter'
    allowedInterfaces:
      - 'App\Repository\RepositoryInterface'
  tags:
    - phpstan.rules.rule

# NeverUseErNamesRule
-
  class: Atournayre\PHPStan\ElegantObject\Rules\NeverUseErNamesRule
  arguments:
    excludedPaths:
      - '/excluded/path'
      - '/excluded/path/*.php'
    excludedSuffixes:
      - 'Handler'
      - 'Controller'
      - 'Helper'
  tags:
    - phpstan.rules.rule

# NeverUseGettersAndSettersRule
-
  class: Atournayre\PHPStan\ElegantObject\Rules\NeverUseGettersAndSettersRule
  arguments:
    excludedPaths:
      - '/excluded/path'
      - '/excluded/path/*.php'
    allowedMethodNames:
      - 'getAttribute'
      - 'getParameter'
    allowedInterfaces:
      - 'Symfony\Component\HttpFoundation\Request'
      - 'App\Contract\DtoInterface'
  tags:
    - phpstan.rules.rule

# NeverUsePublicConstantsRule
-
  class: Atournayre\PHPStan\ElegantObject\Rules\NeverUsePublicConstantsRule
  arguments:
    excludedPaths:
      - '/excluded/path'
      - '/excluded/path/*.php'
    allowedClasses:
      - 'App\Enum\Status'
      - 'Symfony\Component\HttpFoundation\Response'
  tags:
    - phpstan.rules.rule

# KeepInterfacesShortRule
-
  class: Atournayre\PHPStan\ElegantObject\Rules\KeepInterfacesShortRule
  arguments:
    excludedPaths:
      - '/excluded/path'
      - '/excluded/path/*.php'
    maxMethods: 5
  tags:
    - phpstan.rules.rule

# ExposeFewPublicMethodsRule
-
  class: Atournayre\PHPStan\ElegantObject\Rules\ExposeFewPublicMethodsRule
  arguments:
    excludedPaths:
      - '/excluded/path'
      - '/excluded/path/*.php'
    maxPublicMethods: 5
  tags:
    - phpstan.rules.rule

# NeverAcceptNullArgumentsRule
-
  class: Atournayre\PHPStan\ElegantObject\Rules\NeverAcceptNullArgumentsRule
  arguments:
    excludedPaths:
      - '/excluded/path'
      - '/excluded/path/*.php'
    allowedMethodNames:
      - 'allowedMethod'
    allowedParameterNames:
      - 'allowedParameter'
    allowedInterfaces:
      - 'App\Contract\DtoInterface'
  tags:
    - phpstan.rules.rule

```