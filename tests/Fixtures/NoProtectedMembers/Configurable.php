<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoProtectedMembers;

final class Configurable
{
    protected string $allowedProperty = '';

    protected string $reported = '';

    protected function allowedMethod(): void
    {
    }

    protected function reportedMethod(): void
    {
    }
}
