<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Pattern;

final readonly class Pattern
{
    private function __construct(
        private string $pattern = '',
    )
    {
    }

    public static function getter(): self
    {
        return new self('/^get[A-Za-z0-9_]/');
    }

    public static function setter(): self
    {
        return new self('/^set[A-Za-z0-9_]/');
    }

    public function match(string $value): bool
    {
        return (bool) preg_match($this->pattern, $value);
    }
}
