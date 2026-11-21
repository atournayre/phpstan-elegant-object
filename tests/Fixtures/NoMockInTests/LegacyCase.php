<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoMockInTests;

use PHPUnit\Framework\TestCase;

final class LegacyCase extends TestCase
{
    public function testAnything(): void
    {
        $this->createMock(\stdClass::class);
    }
}
