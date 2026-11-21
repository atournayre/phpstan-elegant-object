<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverThrowGenericExceptions;

final class ExcludedClass
{
    public function handle(): void
    {
        throw new \Exception('excluded path');
    }
}
