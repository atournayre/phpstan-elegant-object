<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverAcceptNullArguments;

interface AllowedInterface
{
    public function interfaceMethod(?string $param);

    public function anotherInterfaceMethod($param = null);
}
