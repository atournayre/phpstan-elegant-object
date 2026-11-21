<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoPrimitiveReturn;

final class ExcludedClass
{
    public function handle(): string
    {
        return '';
    }
}
