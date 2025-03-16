<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Traits;

trait PathExclusionTrait
{
    /** @var array<string> */
    protected array $excludedPaths = [];

    public function isExcludedPath(string $file): bool
    {
        foreach ($this->excludedPaths as $excludedPath) {
            if (str_starts_with($file, $excludedPath)) {
                return true;
            }
        }

        return false;
    }
}
