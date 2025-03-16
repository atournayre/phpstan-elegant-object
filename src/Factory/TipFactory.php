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
