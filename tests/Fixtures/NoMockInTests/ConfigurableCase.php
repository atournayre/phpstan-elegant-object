<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoMockInTests;

use PHPUnit\Framework\TestCase;

final class ConfigurableCase extends TestCase
{
    public function testStubAllowed(): void
    {
        $this->createStub(\stdClass::class);
        $this->createMock(\stdClass::class);
    }
}
