<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Sale\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\Sale\Domain\ValueObject\Amount;

final class AmountTest extends TestCase
{
    public function test_creates_amount_with_default_zero(): void
    {
        $amount = new Amount();

        $this->assertSame(0.0, $amount->value());
    }

    public function test_creates_amount_with_positive_value(): void
    {
        $amount = new Amount(12.50);

        $this->assertSame(12.50, $amount->value());
    }

    public function test_amount_is_rounded_to_two_decimals(): void
    {
        $amount = new Amount(1.2345);

        $this->assertSame(1.23, $amount->value());
    }

    public function test_add_returns_sum_as_new_instance(): void
    {
        $a = new Amount(10.00);
        $b = new Amount(5.50);

        $result = $a->add($b);

        $this->assertSame(15.50, $result->value());
        $this->assertSame(10.00, $a->value()); // original immutable
    }

    public function test_subtract_returns_difference_as_new_instance(): void
    {
        $a = new Amount(10.00);
        $b = new Amount(3.00);

        $result = $a->subtract($b);

        $this->assertSame(7.00, $result->value());
    }

    public function test_subtract_can_result_in_negative_amount(): void
    {
        $a = new Amount(3.00);
        $b = new Amount(10.00);

        $result = $a->subtract($b);

        $this->assertSame(-7.00, $result->value());
    }

    public function test_two_amounts_with_same_value_are_equal(): void
    {
        $a = new Amount(5.00);
        $b = new Amount(5.00);

        $this->assertTrue($a->equals($b));
    }
}
