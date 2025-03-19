<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\KeepConstructorsCodeFree;

use Assert\Assertion;

class ClassWithSelfAssertions
{
    private float $price;
    private string $currency;

    public function __construct(float $price, string $currency)
    {
        $this->price = $price;
        $this->currency = $currency;

        $this->assertPositivePrice($price);
        $this->assertValidCurrency($currency);
    }

    private function assertPositivePrice(float $price): void
    {
        if ($price <= 0) {
            throw new \InvalidArgumentException('Price must be positive');
        }
    }

    private function assertValidCurrency(string $currency): void
    {
        $validCurrencies = ['USD', 'EUR', 'GBP'];
        if (!in_array($currency, $validCurrencies, true)) {
            throw new \InvalidArgumentException('Currency must be one of: ' . implode(', ', $validCurrencies));
        }
    }
}

