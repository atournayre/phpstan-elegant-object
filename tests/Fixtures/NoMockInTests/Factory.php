<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoMockInTests;

final class Factory
{
    public function createMock(string $class): object
    {
        return $this->createStub($class);
    }

    public function createStub(string $class): object
    {
        return new \stdClass();
    }
}
