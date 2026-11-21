<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ConstructorObjectsOnly;

final class Example
{
    public function __construct(private string $name, private \DateTimeInterface $at)
    {
    }
}

final class Nullable
{
    public function __construct(private ?int $number)
    {
    }
}

final class Union
{
    public function __construct(private int|string $id)
    {
    }
}

final class Untyped
{
    public function __construct($value)
    {
    }
}

final class Several
{
    public function __construct(array $items, iterable $others, mixed $any, bool $flag, float $ratio)
    {
    }
}

final class Collaborators
{
    public function __construct(private \DateTimeInterface $at, private self|\stdClass|null $other = null)
    {
    }
}

final class NoConstructor
{
    public function handle(string $value): void
    {
    }
}

final class PrivateConstructor
{
    private function __construct(private string $value)
    {
    }

    public static function of(string $value): self
    {
        return new self($value);
    }
}

final class ExampleTest
{
    public function __construct(string $value)
    {
    }
}
