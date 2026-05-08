<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\User\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\User\Domain\ValueObject\PostalCode;

final class PostalCodeTest extends TestCase
{
    public function test_creates_valid_postal_code(): void
    {
        $postalCode = new PostalCode('28001');

        $this->assertSame('28001', $postalCode->value());
    }

    public function test_throws_exception_for_postal_code_with_4_digits(): void
    {
        $this->expectException(InvalidValueException::class);

        new PostalCode('2800');
    }

    public function test_throws_exception_for_postal_code_with_6_digits(): void
    {
        $this->expectException(InvalidValueException::class);

        new PostalCode('280011');
    }

    public function test_throws_exception_for_non_numeric_postal_code(): void
    {
        $this->expectException(InvalidValueException::class);

        new PostalCode('2800a');
    }

    public function test_two_postal_codes_with_same_value_are_equal(): void
    {
        $a = new PostalCode('28001');
        $b = new PostalCode('28001');

        $this->assertTrue($a->equals($b));
    }
}
