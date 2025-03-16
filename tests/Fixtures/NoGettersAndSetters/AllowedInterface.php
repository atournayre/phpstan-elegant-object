<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoGettersAndSetters;

interface AllowedInterface
{
    public function getName(): string;
}
