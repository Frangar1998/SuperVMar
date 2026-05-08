<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\User\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\User\Domain\ValueObject\City;
use SuperVMar\User\Domain\ValueObject\Door;
use SuperVMar\User\Domain\ValueObject\Floor;
use SuperVMar\User\Domain\ValueObject\Name as UserName;
use SuperVMar\User\Domain\ValueObject\Number;
use SuperVMar\User\Domain\ValueObject\Other;
use SuperVMar\User\Domain\ValueObject\Province;
use SuperVMar\User\Domain\ValueObject\Surname;

/**
 * Tests for all remaining User domain string Value Objects:
 * Name, Surname, City, Province, Number, Floor, Door, Other.
 */
final class AddressValueObjectsTest extends TestCase
{

    public function test_user_name_creates_with_valid_value(): void
    {
        $name = new UserName('María');

        $this->assertNotEmpty($name->value());
    }

    public function test_user_name_throws_for_empty_value(): void
    {
        $this->expectException(InvalidValueException::class);

        new UserName('');
    }

    public function test_user_name_throws_when_exceeds_255_chars(): void
    {
        $this->expectException(InvalidValueException::class);

        new UserName(str_repeat('a', 256));
    }


    public function test_surname_creates_with_valid_value(): void
    {
        $surname = new Surname('García López');

        $this->assertNotEmpty($surname->value());
    }

    public function test_surname_throws_for_empty_value(): void
    {
        $this->expectException(InvalidValueException::class);

        new Surname('');
    }

    public function test_surname_throws_when_exceeds_100_chars(): void
    {
        $this->expectException(InvalidValueException::class);

        new Surname(str_repeat('a', 101));
    }


    public function test_city_creates_with_valid_value(): void
    {
        $city = new City('Madrid');

        $this->assertSame('Madrid', $city->value());
    }

    public function test_city_throws_for_empty_value(): void
    {
        $this->expectException(InvalidValueException::class);

        new City('');
    }

    public function test_city_throws_when_exceeds_100_chars(): void
    {
        $this->expectException(InvalidValueException::class);

        new City(str_repeat('a', 101));
    }


    public function test_province_creates_with_valid_value(): void
    {
        $province = new Province('Madrid');

        $this->assertSame('Madrid', $province->value());
    }

    public function test_province_throws_for_empty_value(): void
    {
        $this->expectException(InvalidValueException::class);

        new Province('');
    }

    public function test_province_throws_when_exceeds_100_chars(): void
    {
        $this->expectException(InvalidValueException::class);

        new Province(str_repeat('a', 101));
    }


    public function test_number_creates_with_valid_value(): void
    {
        $number = new Number('42');

        $this->assertSame('42', $number->value());
    }

    public function test_number_throws_for_empty_value(): void
    {
        $this->expectException(InvalidValueException::class);

        new Number('');
    }

    public function test_number_throws_for_non_numeric_value(): void
    {
        $this->expectException(InvalidValueException::class);

        new Number('abc');
    }

    public function test_number_throws_when_exceeds_10_chars(): void
    {
        $this->expectException(InvalidValueException::class);

        new Number(str_repeat('1', 11));
    }


    public function test_floor_creates_with_valid_numeric_value(): void
    {
        $floor = new Floor('3');

        $this->assertSame('3', $floor->value());
    }

    public function test_floor_throws_for_empty_string(): void
    {
        $this->expectException(InvalidValueException::class);

        new Floor('');
    }

    public function test_floor_throws_for_non_numeric_value(): void
    {
        $this->expectException(InvalidValueException::class);

        new Floor('abc');
    }

    public function test_floor_throws_when_exceeds_10_chars(): void
    {
        $this->expectException(InvalidValueException::class);

        new Floor(str_repeat('1', 11));
    }


    public function test_door_creates_with_valid_value(): void
    {
        $door = new Door('A');

        $this->assertSame('A', $door->value());
    }

    public function test_door_throws_when_exceeds_10_chars(): void
    {
        $this->expectException(InvalidValueException::class);

        new Door(str_repeat('a', 11));
    }


    public function test_other_creates_with_valid_value(): void
    {
        $other = new Other('Puerta izquierda');

        $this->assertSame('Puerta izquierda', $other->value());
    }

    public function test_other_throws_when_exceeds_255_chars(): void
    {
        $this->expectException(InvalidValueException::class);

        new Other(str_repeat('a', 256));
    }
}
