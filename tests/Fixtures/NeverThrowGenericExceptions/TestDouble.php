<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverThrowGenericExceptions;

final class TestDouble
{
    public function handle(): void
    {
        throw new \Exception('test classes are skipped');
    }
}
