<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoPrimitiveParameterInService;

final class NotAServiceHandler
{
    public function handle(string $value): void
    {
    }
}

#[\Doctrine\ORM\Mapping\Entity]
final class OrderService
{
    public function handle(string $value): void
    {
    }
}
