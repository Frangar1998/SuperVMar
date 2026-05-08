<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Shared\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\Exception\InvalidUuidValueException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final class IdTest extends TestCase
{
    private const string VALID_UUID = '550e8400-e29b-41d4-a716-446655440000';

    public function test_creates_valid_id(): void
    {
        $id = new Id(self::VALID_UUID);

        $this->assertSame(self::VALID_UUID, $id->value());
    }

    public function test_throws_exception_for_invalid_uuid(): void
    {
        $this->expectException(InvalidUuidValueException::class);

        new Id('not-a-valid-uuid');
    }

    public function test_throws_exception_for_empty_string(): void
    {
        $this->expectException(InvalidUuidValueException::class);

        new Id('');
    }

    public function test_two_ids_with_same_value_are_equal(): void
    {
        $a = new Id(self::VALID_UUID);
        $b = new Id(self::VALID_UUID);

        $this->assertTrue($a->equals($b));
    }

    public function test_two_ids_with_different_values_are_not_equal(): void
    {
        $a = new Id(self::VALID_UUID);
        $b = new Id('660e8400-e29b-41d4-a716-446655440000');

        $this->assertFalse($a->equals($b));
    }

    public function test_random_generates_valid_uuid(): void
    {
        $id = Id::random();

        $this->assertNotEmpty($id->value());
        $this->assertSame($id->value(), (new Id($id->value()))->value());
    }

    public function test_to_string_returns_uuid_value(): void
    {
        $id = new Id(self::VALID_UUID);

        $this->assertSame(self::VALID_UUID, (string) $id);
    }
}
