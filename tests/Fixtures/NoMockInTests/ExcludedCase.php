<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoMockInTests;

use PHPUnit\Framework\TestCase;

final class ExcludedCase extends TestCase
{
    public function testMock(): void
    {
        $this->createMock(\stdClass::class);
    }
}
