<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Product\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\Product\Domain\ValueObject\Stock;
use SuperVMar\Shared\Domain\Exception\InvalidValueException;

final class StockTest extends TestCase
{
    public function test_creates_valid_stock(): void
    {
        $stock = new Stock(100);

        $this->assertSame(100, $stock->value());
    }

    public function test_creates_stock_of_zero(): void
    {
        $stock = new Stock(0);

        $this->assertSame(0, $stock->value());
    }

    public function test_throws_exception_for_negative_stock(): void
    {
        $this->expectException(InvalidValueException::class);

        new Stock(-1);
    }

    public function test_add_returns_sum_as_new_instance(): void
    {
        $stock = new Stock(50);
        $added = new Stock(20);

        $result = $stock->add($added);

        $this->assertSame(70, $result->value());
        $this->assertSame(50, $stock->value()); // original immutable
    }

    public function test_subtract_returns_difference_as_new_instance(): void
    {
        $stock     = new Stock(50);
        $subtracted = new Stock(20);

        $result = $stock->subtract($subtracted);

        $this->assertSame(30, $result->value());
    }

    public function test_subtract_never_goes_below_zero(): void
    {
        $stock     = new Stock(5);
        $subtracted = new Stock(100);

        $result = $stock->subtract($subtracted);

        $this->assertSame(0, $result->value());
    }

    public function test_two_stocks_with_same_value_are_equal(): void
    {
        $a = new Stock(10);
        $b = new Stock(10);

        $this->assertTrue($a->equals($b));
    }
}
