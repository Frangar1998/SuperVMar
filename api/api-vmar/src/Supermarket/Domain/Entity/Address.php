<?php

namespace SuperVMar\Supermarket\Domain\Entity;

use SuperVMar\Supermarket\Domain\ValueObject\City;
use SuperVMar\Supermarket\Domain\ValueObject\Id;
use SuperVMar\Supermarket\Domain\ValueObject\Name;
use SuperVMar\Supermarket\Domain\ValueObject\Number;
use SuperVMar\Supermarket\Domain\ValueObject\PostalCode;
use SuperVMar\Supermarket\Domain\ValueObject\Province;

final readonly class Address
{
    public function __construct(
        private Id         $id,
        private Name       $name,
        private PostalCode $postalCode,
        private City       $city,
        private Number     $number,
        private Province   $province
    ){}

    public function id(): Id
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function postalCode(): PostalCode
    {
        return $this->postalCode;
    }

    public function city(): City
    {
        return $this->city;
    }

    public function number(): Number
    {
        return $this->number;
    }

    public function province(): Province
    {
        return $this->province;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Name($data['name']),
            new PostalCode($data['postalCode']),
            new City($data['city']),
            new Number($data['number']),
            new Province($data['province'])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'postalCode' => $this->postalCode->value(),
            'city' => $this->city->value(),
            'number' => $this->number->value(),
            'province' => $this->province->value()
        ];
    }

    public function compare(self $other): bool
    {
        return $this->name->value() === $other->name()->value()
            && $this->postalCode->value() === $other->postalCode()->value()
            && $this->city->value() === $other->city()->value()
            && $this->number->value() === $other->number()->value()
            && $this->province->value() === $other->province()->value();
    }
}