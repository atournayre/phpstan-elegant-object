<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoProtectedMembers;

abstract class AbstractConfigurable
{
    protected string $value = '';

    abstract protected function hook(): void;
}
