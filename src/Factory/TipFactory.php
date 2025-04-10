<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Factory;

final readonly class TipFactory
{
    private function __construct(
        private string $chapter,
        private string $chapterTitle,
        private ?string $link = null,
    )
    {
    }

    public static function allPropertiesMustBePrivate(): self
    {
        return new self(
            chapter: '3.1',
            chapterTitle: 'All properties must be private',
            link: 'http://goo.gl/8ql2ov',
        );
    }

    public static function gettersAndSetters(): self
    {
        return new self(
            chapter: '3.5',
            chapterTitle: 'Never use getters and setters',
            link: 'http://goo.gl/LSyvo9',
        );
    }

    public static function staticMethods(): self
    {
        return new self(
            chapter: '3.2',
            chapterTitle: 'Don\'t use static methods',
            link: 'http://goo.gl/8ql2ov',
        );
    }

    public static function constructorParametersMustBeObjects(): self
    {
        return new self(
            chapter: 'x.x',
            chapterTitle: 'Constructor parameters must be objects',
        );
    }

    public static function neverUseErNames(): self
    {
        return new self(
            chapter: '1.1',
            chapterTitle: 'Never use -er names',
            link: 'http://goo.gl/Uy3wZ6',
        );
    }

    public static function keepConstructorsCodeFree(): self
    {
        return new self(
            chapter: '1.3',
            chapterTitle: 'Keep constructors code-free',
            link: 'http://goo.gl/DCMFDY',
        );
    }

    public static function alwaysUseInterface(): self
    {
        return new self(
            chapter: '2.3',
            chapterTitle: 'Always use interface',
            link: 'http://goo.gl/vo9F2g',
        );
    }

    public static function neverUsePublicConstants(): self
    {
        return new self(
            chapter: '3.1',
            chapterTitle: 'All properties and constants must be private',
            link: 'http://goo.gl/8ql2ov',
        );
    }

    public static function beEitherFinalOrAbstract(): self
    {
        return new self(
            chapter: '4.3',
            chapterTitle: 'Be either final or abstract',
            link: 'http://goo.gl/vo9F2g',
        );
    }

    public static function neverReturnNull(): self
    {
        return new self(
            chapter: '4.1',
            chapterTitle: 'Never return null',
            link: 'http://goo.gl/TzrYbz',
        );
    }

    public static function beImmutable(): self
    {
        return new self(
            chapter: '2.6',
            chapterTitle: 'Be immutable',
            link: 'http://goo.gl/z1XGjO',
        );
    }

    public static function keepInterfacesShort(): self
    {
        return new self(
            chapter: '2.9',
            chapterTitle: 'Keep interfaces short',
            link: 'http://goo.gl/1Zos9r',
        );
    }

    public static function exposeFewPublicMethods(): self
    {
        return new self(
            chapter: '3.1',
            chapterTitle: 'Expose few public methods',
        );
    }

    public static function neverAcceptNullArguments(): self
    {
        return new self(
            chapter: '3.3',
            chapterTitle: 'Never accept null arguments',
            link: 'http://goo.gl/TzrYbz',
        );
    }

    public static function noNewOutsideSecondaryConstructors(): self
    {
        return new self(
            chapter: '3.6',
            chapterTitle: 'Don\'t use new outside of secondary constructors',
        );
    }

    /**
     * @return array<string>
     */
    public function tips(): array
    {
        $message = sprintf('See "Elegant Objects" by Yegor Bugayenko, Chapter %s "%s"', $this->chapter, $this->chapterTitle);

        if (null !== $this->link) {
            $message = sprintf('%s : %s', $message, $this->link);
        }

        return [$message];
    }
}
