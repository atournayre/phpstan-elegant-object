<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoPrimitiveReturn;

final class Example implements \ArrayAccess
{
    public function __construct(private string $dependency)
    {
    }

    public function plain(): string
    {
        return '';
    }

    public function nullable(): ?int
    {
        return null;
    }

    public function union(): int|string
    {
        return 1;
    }

    public function untyped()
    {
        return 1;
    }

    public function listing(): array
    {
        return [];
    }

    public function predicate(): bool
    {
        return true;
    }

    public function valid(): Value
    {
        return new Value();
    }

    public function nothing(): void
    {
    }

    public function fluent(): self
    {
        return $this;
    }

    public function late(): static
    {
        return $this;
    }

    public function fails(): never
    {
        throw new \LogicException();
    }

    public function status(): Status
    {
        return Status::Active;
    }

    public function stream(): \Traversable
    {
        return new \ArrayIterator([]);
    }

    private function hidden(): string
    {
        return '';
    }

    protected function guarded(): string
    {
        return '';
    }

    public function __toString(): string
    {
        return '';
    }

    public function __invoke(): int
    {
        return 1;
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
    public function impose(): string;
}

final class Implementation implements Contract
{
    public function impose(): string
    {
        return '';
    }
}

abstract class Base
{
    public function template(): int
    {
        return 1;
    }
}

final class Child extends Base
{
    public function template(): int
    {
        return 2;
    }
}
