<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Sale\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\Sale\Domain\ValueObject\Quantity;

final class QuantityTest extends TestCase
{
    public function test_creates_quantity_with_positive_value(): void
    {
        $qty = new Quantity(5);

        $this->assertSame(5, $qty->value());
    }

    public function test_creates_quantity_of_zero(): void
    {
        $qty = new Quantity(0);

        $this->assertSame(0, $qty->value());
    }

    public function test_quantity_accepts_negative_values_by_design(): void
    {
        $qty = new Quantity(-3);

        $this->assertSame(-3, $qty->value());
    }

    public function test_add_returns_sum_as_new_instance(): void
    {
        $a = new Quantity(4);
        $b = new Quantity(3);

        $result = $a->add($b);

        $this->assertSame(7, $result->value());
        $this->assertSame(4, $a->value()); // original immutable
    }

    public function test_subtract_returns_difference_as_new_instance(): void
    {
        $a = new Quantity(10);
        $b = new Quantity(4);

        $result = $a->subtract($b);

        $this->assertSame(6, $result->value());
    }

    public function test_subtract_never_goes_below_zero(): void
    {
        $a = new Quantity(2);
        $b = new Quantity(10);

        $result = $a->subtract($b);

        $this->assertSame(0, $result->value());
    }

    public function test_two_quantities_with_same_value_are_equal(): void
    {
        $a = new Quantity(5);
        $b = new Quantity(5);

        $this->assertTrue($a->equals($b));
    }
}
