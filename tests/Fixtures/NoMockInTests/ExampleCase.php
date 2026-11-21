<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoMockInTests;

use PHPUnit\Framework\TestCase;

final class ExampleCase extends TestCase
{
    public function testMocks(): void
    {
        $this->createMock(\stdClass::class);
        $this->createStub(\stdClass::class);
        $this->createConfiguredMock(\stdClass::class, []);
        $this->createPartialMock(\stdClass::class, []);
        $this->getMockBuilder(\stdClass::class);
        $this->createMockForIntersectionOfInterfaces([\Countable::class, \Traversable::class]);
        $this->prophesize(\stdClass::class);
        \Mockery::mock(\stdClass::class);
        \Mockery::spy(\stdClass::class);
    }

    public function testFake(): void
    {
        $fake = new \ArrayObject();
        $this->assertCount(0, $fake);
    }
}
