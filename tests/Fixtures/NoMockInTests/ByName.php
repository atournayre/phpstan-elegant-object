<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoMockInTests;

final class ByNameTest
{
    public function createMock(string $class): object
    {
        return new \stdClass();
    }

    public function run(): void
    {
        $this->createMock(\stdClass::class);
    }
}
