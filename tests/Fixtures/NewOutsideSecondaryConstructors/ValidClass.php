<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NewOutsideSecondaryConstructors;

class ValidClass
{
    public function __construct()
    {
        // new is allowed in constructor
        $date = new \DateTime();
    }

    public static function newInstance(): self
    {
        // new is allowed in static methods starting with "new"
        return new self();
    }

    public static function fromArray(array $data): self
    {
        // new is allowed in static methods starting with "from"
        return new self();
    }

    public static function createFromString(string $data): self
    {
        // new is allowed in static methods starting with "create"
        return new self();
    }

    public static function withParameters(string $param1, string $param2): self
    {
        // new is allowed in static methods starting with "with"
        return new self();
    }

    public static function ofType(string $type): self
    {
        // new is allowed in static methods starting with "of"
        return new self();
    }
}
