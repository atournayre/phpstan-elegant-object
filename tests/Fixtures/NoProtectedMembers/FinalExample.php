<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoProtectedMembers;

final class FinalExample
{
    protected const LIMIT = 1;

    protected string $name = 'a';

    private string $hidden = 'b';

    public function __construct(
        protected int $promoted = 0,
        private int $kept = 0,
    ) {
    }

    protected function helper(): void
    {
    }

    public function open(): void
    {
    }
}
