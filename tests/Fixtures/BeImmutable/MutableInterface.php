<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\BeImmutable;

interface MutableInterface
{
    public function modifyProperty(string $newValue): void;
}
