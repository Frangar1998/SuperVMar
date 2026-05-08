<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Product\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\Product\Domain\ValueObject\Ean;
use SuperVMar\Shared\Domain\Exception\InvalidValueException;

final class EanTest extends TestCase
{
    public function test_creates_valid_ean(): void
    {
        $ean = new Ean('8410188018090');

        $this->assertSame('8410188018090', $ean->value());
    }

    public function test_creates_ean_with_less_than_13_digits(): void
    {
        $ean = new Ean('12345');

        $this->assertSame('12345', $ean->value());
    }

    public function test_throws_exception_for_empty_ean(): void
    {
        $this->expectException(InvalidValueException::class);

        new Ean('');
    }

    public function test_throws_exception_for_non_numeric_ean(): void
    {
        $this->expectException(InvalidValueException::class);

        new Ean('abc123');
    }

    public function test_throws_exception_when_ean_exceeds_13_characters(): void
    {
        $this->expectException(InvalidValueException::class);

        new Ean(str_repeat('1', 14));
    }

    public function test_ean_at_exactly_13_characters_is_valid(): void
    {
        $ean = new Ean(str_repeat('1', 13));

        $this->assertSame(13, strlen($ean->value()));
    }

    public function test_two_eans_with_same_value_are_equal(): void
    {
        $a = new Ean('1234567');
        $b = new Ean('1234567');

        $this->assertTrue($a->equals($b));
    }
}
