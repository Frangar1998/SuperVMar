<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Product\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\Product\Domain\ValueObject\Active;
use SuperVMar\Shared\Domain\Exception\InvalidValueException;

final class ActiveTest extends TestCase
{
    public function test_creates_with_value_zero(): void
    {
        $active = new Active(0);

        $this->assertSame(0, $active->value());
    }

    public function test_creates_with_value_one(): void
    {
        $active = new Active(1);

        $this->assertSame(1, $active->value());
    }

    public function test_throws_exception_for_value_greater_than_one(): void
    {
        $this->expectException(InvalidValueException::class);

        new Active(2);
    }

    public function test_throws_exception_for_negative_value(): void
    {
        $this->expectException(InvalidValueException::class);

        new Active(-1);
    }

    public function test_two_active_values_with_same_state_are_equal(): void
    {
        $a = new Active(1);
        $b = new Active(1);

        $this->assertTrue($a->equals($b));
    }
}
