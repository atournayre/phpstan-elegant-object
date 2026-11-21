<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoProtectedMembers;

final class Child extends Base
{
    protected const SHARED = 2;

    protected string $shared = 'y';

    protected function hook(): void
    {
    }

    protected function overridable(): void
    {
    }

    protected function own(): void
    {
    }
}
