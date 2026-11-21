<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoProtectedMembers;

final class ImplementsTolerant implements TolerantInterface
{
    protected string $value = '';
}
