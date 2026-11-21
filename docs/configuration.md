# Configuration examples

```yaml
services:
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
    
    # ConstructorsMustBePrivateRule
    # Constructors declared by an interface or an abstract parent are always allowed.
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\ConstructorsMustBePrivateRule
      arguments:
        excludedPaths:
          - '/excluded/path'
        excludedNamespaces:
          - 'App\Entity'
          - 'App\Command'
        allowedInterfaces:
          - 'App\Contract\FrameworkInterface'
          - 'Symfony\*\ConstructableInterface'
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

    # NoBooleanFlagArgumentRule
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\NoBooleanFlagArgumentRule
      arguments:
        excludedPaths:
          - '/excluded/path'
      tags:
        - phpstan.rules.rule

    # NoPrimitiveParameterRule
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\NoPrimitiveParameterRule
      arguments:
        excludedPaths:
          - '/excluded/path'
        allowedMethodNames:
          - 'allowedMethod'
        allowedParameterNames:
          - 'allowedParameter'
        allowedInterfaces:
          - 'App\Contract\*Interface'
        allowedClasses:
          - 'App\Legacy\*'
        allowedTypes:
          - 'string' # tolerated primitive types: string, int, float, bool, array, iterable, mixed
      tags:
        - phpstan.rules.rule

    # NoNewOutsideSecondaryConstructorsRule
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\NoNewOutsideSecondaryConstructorsRule
      arguments:
        excludedPaths:
          - '/excluded/path'
          - '/excluded/path/*.php'
        allowedInterfaces:
          - 'App\Contract\DtoInterface'
        excludedClasses:
          - 'App\ExcludedClass'
        secondaryConstructorPrefixes:
          - 'new'
          - 'from'
          - 'create'
          - 'of'
          - 'with'
          - 'build'
      tags:
        - phpstan.rules.rule

    # AvoidTypeIntrospectionRule
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\AvoidTypeIntrospectionRule
      arguments:
        excludedPaths:
          - '/excluded/path'
        allowedMethodNames:
          - 'equals'
        allowedInterfaces:
          - 'App\Contract\*Interface'
        allowedClasses:
          - 'App\Legacy\*'
      tags:
        - phpstan.rules.rule

    # NoMockInTestsRule
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\NoMockInTestsRule
      arguments:
        excludedPaths:
          - '/excluded/path'
        allowedMethodNames:
          - 'createStub'
        allowedClasses:
          - 'App\Tests\Legacy\*'
      tags:
        - phpstan.rules.rule

    # MaxPropertiesRule
    # Counts instance properties (promoted ones included). The default of 4 is a configurable guess:
    # the exact threshold of the "encapsulate as little as possible" principle is not verified.
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\MaxPropertiesRule
      arguments:
        excludedPaths:
          - '/excluded/path'
        maxProperties: 4
        allowedClasses:
          - 'App\Legacy\*'
        allowedInterfaces:
          - 'App\Contract\*Interface'
      tags:
        - phpstan.rules.rule

```
```