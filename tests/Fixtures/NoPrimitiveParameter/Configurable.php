<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoPrimitiveParameter;

final class Configurable
{
    public function allowedMethod(string $value): void
    {
    }

    public function allowedParameter(string $skipped, int $reported): void
    {
    }

    public function allowedType(string $text, int $number): void
    {
    }
}

final class AllowedByClass
{
    public function handle(string $value): void
    {
    }
}

interface TolerantInterface
{
}

final class AllowedByInterface implements TolerantInterface
{
    public function handle(string $value): void
    {
    }
}
