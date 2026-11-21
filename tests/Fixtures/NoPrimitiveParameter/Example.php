<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoPrimitiveParameter;

final class Example implements \ArrayAccess
{
    public function __construct(private string $dependency)
    {
    }

    public function plain(string $name, \DateTimeInterface $at): void
    {
    }

    public function nullable(?int $number): void
    {
    }

    public function union(int|string $id): void
    {
    }

    public function untyped($value): void
    {
    }

    public function listing(array $items, iterable $others): void
    {
    }

    public function valid(Value $value, Status $status, \Closure $callback, ?Value $optional, \Traversable $stream): void
    {
    }

    public function noParameter(): void
    {
    }

    private function hidden(string $value): void
    {
    }

    protected function guarded(string $value): void
    {
    }

    public function __call(string $name, array $arguments): void
    {
    }

    public function __invoke(string $value): void
    {
    }

    public function offsetExists(mixed $offset): bool
    {
        return true;
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $offset;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}

final class Value
{
}

enum Status: string
{
    case Active = 'active';
}

interface Contract
{
    public function impose(string $value): void;
}

final class Implementation implements Contract
{
    public function impose(string $value): void
    {
    }
}

abstract class Base
{
    public function template(int $value): void
    {
    }
}

final class Child extends Base
{
    public function template(int $value): void
    {
    }
}
