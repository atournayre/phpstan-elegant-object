<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverReturnNull;

class ImplementingClass implements NullReturnInterface
{
    // This should not trigger the rule due to interface implementation
    public function returnNull(): ?string
    {
        return null;
    }
}
