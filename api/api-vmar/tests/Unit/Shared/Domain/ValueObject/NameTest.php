<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Shared\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\Name;

final class NameTest extends TestCase
{
    public function test_creates_valid_name(): void
    {
        $name = new Name('Leche Entera');

        $this->assertSame('Leche Entera', $name->value());
    }

    public function test_throws_exception_for_empty_name(): void
    {
        $this->expectException(InvalidValueException::class);

        new Name('');
    }

    public function test_throws_exception_when_name_exceeds_max_length(): void
    {
        $this->expectException(InvalidValueException::class);

        new Name(str_repeat('a', 256));
    }

    public function test_name_at_max_length_is_valid(): void
    {
        $name = new Name(str_repeat('a', 255));

        $this->assertSame(255, strlen($name->value()));
    }

    public function test_two_names_with_same_value_are_equal(): void
    {
        $a = new Name('Producto');
        $b = new Name('Producto');

        $this->assertTrue($a->equals($b));
    }

    public function test_to_string_returns_name_value(): void
    {
        $name = new Name('Producto');

        $this->assertSame('Producto', (string) $name);
    }
}
