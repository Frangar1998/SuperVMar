<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\User\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\User\Domain\ValueObject\Username;

final class UsernameTest extends TestCase
{
    public function test_creates_valid_username(): void
    {
        $username = new Username('john_doe');

        $this->assertSame('john_doe', $username->value());
    }

    public function test_throws_exception_for_empty_username(): void
    {
        $this->expectException(InvalidValueException::class);

        new Username('');
    }

    public function test_throws_exception_when_username_exceeds_100_characters(): void
    {
        $this->expectException(InvalidValueException::class);

        new Username(str_repeat('a', 101));
    }

    public function test_username_at_max_length_is_valid(): void
    {
        $username = new Username(str_repeat('a', 100));

        $this->assertSame(100, strlen($username->value()));
    }

    public function test_two_usernames_with_same_value_are_equal(): void
    {
        $a = new Username('john');
        $b = new Username('john');

        $this->assertTrue($a->equals($b));
    }
}
