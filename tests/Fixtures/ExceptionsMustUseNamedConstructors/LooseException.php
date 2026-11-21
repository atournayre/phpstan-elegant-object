<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\ExceptionsMustUseNamedConstructors;

final class LooseException extends \RuntimeException
{
    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
