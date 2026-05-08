<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\User\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\User\Domain\ValueObject\IsAdmin;

final class IsAdminTest extends TestCase
{
    public function test_creates_with_value_zero(): void
    {
        $isAdmin = new IsAdmin(0);

        $this->assertSame(0, $isAdmin->value());
    }

    public function test_creates_with_value_one(): void
    {
        $isAdmin = new IsAdmin(1);

        $this->assertSame(1, $isAdmin->value());
    }

    public function test_throws_exception_for_value_two(): void
    {
        $this->expectException(InvalidValueException::class);

        new IsAdmin(2);
    }

    public function test_throws_exception_for_negative_value(): void
    {
        $this->expectException(InvalidValueException::class);

        new IsAdmin(-1);
    }

    public function test_two_is_admin_values_with_same_state_are_equal(): void
    {
        $a = new IsAdmin(1);
        $b = new IsAdmin(1);

        $this->assertTrue($a->equals($b));
    }

    public function test_admin_and_non_admin_are_not_equal(): void
    {
        $admin     = new IsAdmin(1);
        $nonAdmin  = new IsAdmin(0);

        $this->assertFalse($admin->equals($nonAdmin));
    }
}
