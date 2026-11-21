<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoPrimitiveReturn;

final class Configurable
{
    public function allowedMethod(): string
    {
        return '';
    }

    public function allowedPredicate(): bool
    {
        return true;
    }

    public function stillReported(): int
    {
        return 1;
    }
}

final class AllowedByClass
{
    public function handle(): string
    {
        return '';
    }
}

interface TolerantInterface
{
}

final class AllowedByInterface implements TolerantInterface
{
    public function handle(): string
    {
        return '';
    }
}
