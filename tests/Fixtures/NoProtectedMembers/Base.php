<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoProtectedMembers;

abstract class Base
{
    protected const SHARED = 1;

    protected string $shared = 'x';

    protected int $extra = 0;

    abstract protected function hook(): void;

    protected function concrete(): void
    {
    }

    protected function overridable(): void
    {
    }
}
