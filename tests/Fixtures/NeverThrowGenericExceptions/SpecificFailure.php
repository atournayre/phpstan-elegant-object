<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverThrowGenericExceptions;

final class SpecificFailure extends \RuntimeException implements DomainFailure
{
}
