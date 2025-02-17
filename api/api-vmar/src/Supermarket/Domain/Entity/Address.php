<?php

namespace SuperVMar\Supermarket\Domain\Entity;

use Doctrine\Persistence\Proxy;
use SuperVMar\Supermarket\Domain\ValueObject\City;
use SuperVMar\Supermarket\Domain\ValueObject\Door;
use SuperVMar\Supermarket\Domain\ValueObject\Floor;
use SuperVMar\Supermarket\Domain\ValueObject\Id;
use SuperVMar\Supermarket\Domain\ValueObject\Name;
use SuperVMar\Supermarket\Domain\ValueObject\Number;
use SuperVMar\Supermarket\Domain\ValueObject\Other;
use SuperVMar\Supermarket\Domain\ValueObject\PostalCode;
use SuperVMar\Supermarket\Domain\ValueObject\Province;

final readonly class Address
{
    public function __construct(
        private Id $id, 
        private Name $name, 
        private PostalCode $postal_code, 
        private City $city, 
        private Number $number, 
        private Province $province
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
        return $this->postal_code;
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
            new PostalCode($data['postal_code']),
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
            'postal_code' => $this->postal_code->value(),
            'city' => $this->city->value(),
            'number' => $this->number->value(),
            'province' => $this->province->value()
        ];
    }
}