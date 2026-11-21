<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ExceptionsMustUseNamedConstructors;

final class AllowedByNameException extends \Exception
{
    public function __construct()
    {
        parent::__construct();
    }
}
