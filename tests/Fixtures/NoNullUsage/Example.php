<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoNullUsage;

final class Example
{
    private ?string $nullable = 'a';
    private string|null $union = 'a';
    private $untyped = null;
    private string $fine = 'a';
    private ?string $allowedProperty = null;

    public function __construct(private ?int $promoted = 1)
    {
    }

    public function comparisons(mixed $value): bool
    {
        $a = $value === null;
        $b = $value !== null;
        $c = null == $value;
        $d = $value != null;

        return $a || $b || $c || $d || is_null($value);
    }

    public function nullsafe(?object $value): mixed
    {
        $property = $value?->property;

        return $value?->method() ?? $property;
    }

    public function coalescing(array $values): mixed
    {
        $values['a'] ??= 'default';

        return $values['b'] ?? 'default';
    }

    public function allowedMethod(mixed $value): bool
    {
        return $value === null;
    }
}
