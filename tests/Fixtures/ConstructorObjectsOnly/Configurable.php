<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ConstructorObjectsOnly;

final class Configurable
{
    public function __construct(string $skipped, int $reported)
    {
    }
}

final class TypeAllowed
{
    public function __construct(string $text, int $number)
    {
    }
}

final class AllowedByClass
{
    public function __construct(string $value)
    {
    }
}

interface TolerantInterface
{
}

final class AllowedByInterface implements TolerantInterface
{
    public function __construct(string $value)
    {
    }
}
