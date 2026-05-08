<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Product\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\Product\Domain\ValueObject\Percent;
use SuperVMar\Shared\Domain\Exception\InvalidValueException;

final class PercentTest extends TestCase
{
    public function test_creates_valid_percent(): void
    {
        $percent = new Percent(21.0);

        $this->assertSame(21.0, $percent->value());
    }

    public function test_creates_percent_of_zero(): void
    {
        $percent = new Percent(0.0);

        $this->assertSame(0.0, $percent->value());
    }

    public function test_throws_exception_for_negative_percent(): void
    {
        $this->expectException(InvalidValueException::class);

        new Percent(-0.01);
    }

    public function test_percent_is_rounded_to_two_decimals(): void
    {
        $percent = new Percent(21.555);

        $this->assertSame(21.56, $percent->value());
    }

    public function test_to_string_percent_includes_symbol(): void
    {
        $percent = new Percent(21.0);

        $this->assertStringContainsString('%', $percent->toStringPercent());
        $this->assertStringContainsString('21', $percent->toStringPercent());
    }

    public function test_two_percents_with_same_value_are_equal(): void
    {
        $a = new Percent(10.0);
        $b = new Percent(10.0);

        $this->assertTrue($a->equals($b));
    }
}
