<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\User\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\User\Domain\Exception\InvalidPasswordException;
use SuperVMar\User\Domain\ValueObject\Password;

/**
 * Valid password used across tests:
 *   - Length 14 (≥ 12 required)
 *   - Contains uppercase (T)
 *   - Contains lowercase (est, assword)
 *   - Contains digit (1)
 *   - Contains special char '_' (from allowed set: #?!@$%^&*_)
 */
final class PasswordTest extends TestCase
{
    private const string VALID_PLAIN = 'TestPassword1_';

    public function test_creates_valid_password(): void
    {
        $password = new Password(self::VALID_PLAIN, self::VALID_PLAIN);

        $this->assertTrue(password_verify(self::VALID_PLAIN, $password->valueEncoded()));
    }

    public function test_throws_exception_when_passwords_do_not_match(): void
    {
        $this->expectException(InvalidPasswordException::class);

        new Password(self::VALID_PLAIN, 'DifferentPass1_');
    }

    public function test_throws_exception_when_password_is_too_short(): void
    {
        $this->expectException(InvalidPasswordException::class);

        new Password('Short1_Pass', 'Short1_Pass');
    }

    public function test_throws_exception_when_password_has_no_uppercase(): void
    {
        $this->expectException(InvalidPasswordException::class);

        new Password('testpassword1_', 'testpassword1_');
    }

    public function test_throws_exception_when_password_has_no_lowercase(): void
    {
        $this->expectException(InvalidPasswordException::class);

        new Password('TESTPASSWORD1_', 'TESTPASSWORD1_');
    }

    public function test_throws_exception_when_password_has_no_digit(): void
    {
        $this->expectException(InvalidPasswordException::class);

        new Password('TestPassword__', 'TestPassword__');
    }

    public function test_throws_exception_when_password_has_no_special_character(): void
    {
        $this->expectException(InvalidPasswordException::class);

        new Password('TestPassword12', 'TestPassword12');
    }

    public function test_encoded_value_is_a_bcrypt_hash(): void
    {
        $password = new Password(self::VALID_PLAIN, self::VALID_PLAIN);
        $info     = password_get_info($password->valueEncoded());

        $this->assertSame(PASSWORD_BCRYPT, $info['algo']);
    }

    public function test_equals_compares_plain_against_encoded(): void
    {
        $original  = new Password(self::VALID_PLAIN, self::VALID_PLAIN);
        $same      = new Password(self::VALID_PLAIN, self::VALID_PLAIN);
        $different = new Password('AnotherPass1_', 'AnotherPass1_');

        $this->assertTrue($original->equals($same));
        $this->assertFalse($original->equals($different));
    }
}
