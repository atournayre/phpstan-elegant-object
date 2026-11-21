<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ExceptionsMustUseNamedConstructors;

final class AllowedByInterfaceException extends \Exception implements TolerantInterface
{
    public function __construct()
    {
        parent::__construct();
    }
}
