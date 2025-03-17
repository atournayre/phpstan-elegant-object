> This extension is a work in progress. Use at your own risk.

# PHPStan Elegant Object Rules

This PHPStan extension adds custom rules to encourage and validate elegant object design principles in your PHP code.

It's based on [Yegor Bugayenko](https://github.com/yegor256)'s [books](https://www.yegor256.com/books.html) :
- Elegant Objects (Volume 1)
- Elegant Objects (Volume 2)

## Installation

```bash
composer require --dev atournayre/phpstan-elegant-object
```

## Configuration

Include the extension in your phpstan.neon file:

```yaml
# Include the extension config if you want extra strict rules
includes:
    - vendor/atournayre/phpstan-elegant-object/phpstan.neon
```

> It is recommended to configure each rule separately.

## Available Rules

See [configuration](docs/configuration.md) for a list of available rules.

## Creating Custom Rules

To create a new rule:

1. Create a class in the `src/Analyzer/` folder that implements `Atournayre\PHPStan\ElegantObject\Analyzer\RuleAnalyzer`
2. Define the node type to analyze with `getNodeType()`
3. Override `shouldSkipAnalysis()` if necessary
4. Implement your analysis logic in `analyze()`
5. Create a class in the `src/Rules/` folder that extends `Atournayre\PHPStan\ElegantObject\Rules\ComposableRule` (see existing rules for examples)
6. Register your rule in `phpstan.neon`

## Tests

To run the tests:

```bash
vendor/bin/phpunit
```

## Rules

From the book:
- [x] Never use -er names (chapter 1.1)
- [x] Keep constructors code-free (chapter 1.3)
- [x] Always use interface (chapter 2.3)
- [x] Don't use public constant (chapter 2.5)
- [x] Be immutable (chapter 2.6)
- [ ] Keep interfaces short (chapter 2.9)
- [ ] Expose fewer than 5 public methods (chapter 3.1)
- [x] Don't use static methods (chapter 3.2)
- [ ] Never accept null arguments (chapter 3.3)
- [x] Never use getters and setters (chapter 3.5)
- [ ] Don't use new outside of secondary constructors (chapter 3.6)
- [ ] Avoid type introspection and casting (chapter 3.7)
- [x] Never return Null (chapter 4.1)
- [ ] Throw only checked exceptions (chapter 4.2)
- [x] Be either final or abstract (chapter 4.3)
- [ ] Private static literals (chapter 5.4)

Other:
- [x] All properties must be private
- [ ] Constructor objects only
