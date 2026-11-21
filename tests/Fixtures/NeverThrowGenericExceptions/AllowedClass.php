<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverThrowGenericExceptions;

final class AllowedClass
{
    public function handle(): void
    {
        throw new \Exception('class allowed');
    }
}
