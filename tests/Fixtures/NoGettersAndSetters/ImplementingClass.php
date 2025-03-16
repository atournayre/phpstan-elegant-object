<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoGettersAndSetters;

class ImplementingClass implements AllowedInterface
{
    public function getName(): string
    {
        return 'name';
    }
}
