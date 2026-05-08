<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\User\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\User\Domain\ValueObject\Phone;

final class PhoneTest extends TestCase
{
    public function test_creates_valid_phone_number(): void
    {
        $phone = new Phone('612345678');

        $this->assertSame('612345678', $phone->value());
    }

    public function test_throws_exception_for_phone_with_8_digits(): void
    {
        $this->expectException(InvalidValueException::class);

        new Phone('61234567');
    }

    public function test_throws_exception_for_phone_with_10_digits(): void
    {
        $this->expectException(InvalidValueException::class);

        new Phone('6123456789');
    }

    public function test_throws_exception_for_phone_with_letters(): void
    {
        $this->expectException(InvalidValueException::class);

        new Phone('61234567a');
    }

    public function test_throws_exception_for_empty_phone(): void
    {
        $this->expectException(InvalidValueException::class);

        new Phone('');
    }

    public function test_two_phones_with_same_value_are_equal(): void
    {
        $a = new Phone('612345678');
        $b = new Phone('612345678');

        $this->assertTrue($a->equals($b));
    }
}
