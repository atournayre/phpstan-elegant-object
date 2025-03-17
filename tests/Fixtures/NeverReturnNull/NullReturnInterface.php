<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverReturnNull;

interface NullReturnInterface
{
    public function returnNull(): ?string;
}
