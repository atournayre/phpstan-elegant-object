<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverUseGettersAndSetters;

interface AllowedInterface
{
    public function getName(): string;
}
