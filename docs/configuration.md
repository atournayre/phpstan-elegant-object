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

    # NoStaticPropertiesRule
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\NoStaticPropertiesRule
      arguments:
        excludedPaths:
          - '/excluded/path'
        allowedPropertiesNames: # applies to static properties, static variables and global variables
          - 'instance'
        allowedInterfaces:
          - 'App\Contract\*Interface'
        allowedClasses:
          - 'App\Legacy\*'
      tags:
        - phpstan.rules.rule

    # NoNullUsageRule
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\NoNullUsageRule
      arguments:
        excludedPaths:
          - '/excluded/path'
        allowedPropertyNames:
          - 'allowedProperty'
        allowedMethodNames:
          - 'allowedMethod'
        allowedInterfaces:
          - 'App\Contract\*Interface'
        checkProperties: true  # nullable property types, null defaults
        checkComparisons: true # === null, !== null, == null, != null, is_null()
        checkNullsafe: true    # ?-> property fetch and method call
      tags:
        - phpstan.rules.rule

    # NoEmptyCatchRule
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\NoEmptyCatchRule
      arguments:
        excludedPaths:
          - '/excluded/path'
        allowedExceptions:
          - 'JsonException'
          - 'App\Exception\Ignorable*' # wildcards; every caught type must match
        allowedMethodNames:
          - 'tryClose'
        allowedClasses:
          - 'App\Legacy\Cleaner'
      tags:
        - phpstan.rules.rule

    # CatchMustRethrowRule
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\CatchMustRethrowRule
      arguments:
        excludedPaths:
          - '/excluded/path'
        allowedExceptions:
          - 'JsonException' # catch types exempt, wildcards supported; default: none
        allowedMethodNames:
          - 'allowedMethod'
        allowedClasses:
          - 'App\Legacy\*'
        allowedInterfaces:
          - 'App\Contract\*Interface'
      tags:
        - phpstan.rules.rule

    # ConstructorObjectsOnlyRule
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\ConstructorObjectsOnlyRule
      arguments:
        excludedPaths:
          - '/excluded/path'
        allowedParameterNames:
          - 'allowedParameter'
        allowedInterfaces:
          - 'App\Contract\*Interface'
        allowedClasses:
          - 'App\Legacy\*'
        allowedTypes:
          - 'string' # tolerated primitive types: string, int, float, bool, array, iterable, mixed
        checkPrivateConstructors: false # default; true also checks private constructors (named-constructor classes)
      tags:
        - phpstan.rules.rule

    # NoPrimitiveReturnRule
    # Very strict, opt-in: public methods must not return string/int/float/bool/array/iterable/mixed (or no type).
    # void, never, self, static and object types are not primitive. bool is flagged unless listed in allowedTypes.
    # Methods whose signature is imposed by a parent class or interface are ignored.
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\NoPrimitiveReturnRule
      arguments:
        excludedPaths:
          - '/excluded/path'
        allowedMethodNames:
          - 'allowedMethod'
        allowedInterfaces:
          - 'App\Contract\*Interface'
        allowedClasses:
          - 'App\Legacy\*'
        allowedTypes:
          - 'bool' # tolerated primitive types: string, int, float, bool, array, iterable, mixed
      tags:
        - phpstan.rules.rule

    # ForbiddenNamePatternsRule
    # Default patterns: Abstract*, Base*, *Impl, *Util, *Utils, *Data, *Info (globs on the short name, case-sensitive).
    # A trailing '*' requires an uppercase letter, digit, underscore or the end of the name: Abstract* matches
    # AbstractFoo, not Abstraction; Base* matches BaseFoo and Base, not Baseline.
    # *Helper and *Manager are already flagged by NeverUseErNamesRule (-er suffix) and are not repeated here.
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\ForbiddenNamePatternsRule
      arguments:
        excludedPaths:
          - '/excluded/path'
        forbiddenPatterns: # replaces the default patterns when given
          - 'Abstract*'
          - '*Impl'
        allowedClasses: # wildcards on the fully qualified name
          - 'App\Legacy\*'
        allowedInterfaces: # wildcards on the fully qualified name
          - 'App\Contract\*Info'
      tags:
        - phpstan.rules.rule

    # NoProtectedMembersRule
    # Flags protected methods, properties and constants. Always flagged in final classes; in abstract classes
    # too, except protected abstract methods (template method hooks) or when allowInAbstractClasses is true.
    # Members overriding a parent member and test classes are ignored.
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\NoProtectedMembersRule
      arguments:
        excludedPaths:
          - '/excluded/path'
        allowedMethodNames:
          - 'allowedMethod'
        allowedPropertyNames:
          - 'allowedProperty'
        allowedInterfaces:
          - 'App\Contract\*Interface'
        allowedClasses:
          - 'App\Legacy\LegacyClass'
        allowInAbstractClasses: false # default: abstract classes are flagged too
      tags:
        - phpstan.rules.rule

    # NeverThrowGenericExceptionsRule (opt-in project convention, deviates from EO 4.2.5)
    -
      class: Atournayre\PHPStan\ElegantObject\Rules\NeverThrowGenericExceptionsRule
      arguments:
        excludedPaths:
          - '/excluded/path'
        allowedExceptions:
          - 'App\Exception\*' # wildcards, tolerated even if listed as generic
        allowedMethodNames:
          - 'allowedMethod'
        allowedClasses:
          - 'App\Legacy\LegacyService' # exact class names
        requiredInterface: 'App\Exception\DomainExceptionInterface' # optional: every thrown class must implement it
      tags:
        - phpstan.rules.rule

```