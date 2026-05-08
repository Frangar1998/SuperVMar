<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\User\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\User\Domain\ValueObject\Email;

final class EmailTest extends TestCase
{
    public function test_creates_valid_email(): void
    {
        $email = new Email('user@example.com');

        $this->assertSame('user@example.com', $email->value());
    }

    public function test_throws_exception_for_invalid_email_format(): void
    {
        $this->expectException(InvalidValueException::class);

        new Email('not-an-email');
    }

    public function test_throws_exception_for_email_with_only_at_sign(): void
    {
        $this->expectException(InvalidValueException::class);

        new Email('@');
    }

    public function test_throws_exception_when_email_exceeds_100_characters(): void
    {
        $this->expectException(InvalidValueException::class);

        $local = str_repeat('a', 96);
        new Email($local . '@b.es');
    }

    public function test_two_emails_with_same_value_are_equal(): void
    {
        $a = new Email('user@example.com');
        $b = new Email('user@example.com');

        $this->assertTrue($a->equals($b));
    }
}
