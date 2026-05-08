<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Product\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\Product\Domain\ValueObject\Price;
use SuperVMar\Shared\Domain\Exception\InvalidValueException;

final class PriceTest extends TestCase
{
    public function test_creates_valid_price(): void
    {
        $price = new Price(1.29);

        $this->assertSame(1.29, $price->value());
    }

    public function test_creates_price_of_zero(): void
    {
        $price = new Price(0.0);

        $this->assertSame(0.0, $price->value());
    }

    public function test_price_is_rounded_to_two_decimals(): void
    {
        $price = new Price(1.2345);

        $this->assertSame(1.23, $price->value());
    }

    public function test_throws_exception_for_negative_price(): void
    {
        $this->expectException(InvalidValueException::class);

        new Price(-0.01);
    }

    public function test_throws_exception_for_large_negative_price(): void
    {
        $this->expectException(InvalidValueException::class);

        new Price(-100.0);
    }

    public function test_two_prices_with_same_value_are_equal(): void
    {
        $a = new Price(2.50);
        $b = new Price(2.50);

        $this->assertTrue($a->equals($b));
    }
}
