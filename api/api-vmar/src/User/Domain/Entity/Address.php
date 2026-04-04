<?php

namespace SuperVMar\User\Domain\Entity;

use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\User\Domain\ValueObject\City;
use SuperVMar\User\Domain\ValueObject\Door;
use SuperVMar\User\Domain\ValueObject\Floor;
use SuperVMar\User\Domain\ValueObject\Name;
use SuperVMar\User\Domain\ValueObject\Number;
use SuperVMar\User\Domain\ValueObject\Other;
use SuperVMar\User\Domain\ValueObject\PostalCode;
use SuperVMar\User\Domain\ValueObject\Province;

final readonly class Address
{
    public function __construct(
        private Id         $id,
        private Name       $name,
        private PostalCode $postalCode,
        private City       $city,
        private Number     $number,
        private Province   $province,
        private ?Floor     $floor = null, 
        private ?Door      $door = null,
        private ?Other     $other = null,
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
    
    public function floor(): ?Floor
    {
        return $this->floor;
    }
    
    public function door(): ?Door
    {
        return $this->door;
    }
    
    public function other(): ?Other
    {
        return $this->other;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Name($data['name']),
            new PostalCode($data['postalCode']),
            new City($data['city']),
            new Number($data['number']),
            new Province($data['province']),
            !empty($data['floor']) ? new Floor($data['floor']) : null,
            !empty($data['door']) ? new Door($data['door']) : null,
            !empty($data['other']) ? new Other($data['other']) : null
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
            'province' => $this->province->value(),
            'floor' => $this->floor?->value(),
            'door' => $this->door?->value(),
            'other' => $this->other?->value()
        ];
    }

    public function compare(self $other): bool
    {
        return $this->name->equals($other->name())
            && $this->postalCode->equals($other->postalCode())
            && $this->city->equals($other->city())
            && $this->number->equals($other->number())
            && $this->province->equals($other->province())
            && $this->floor?->value() === $other->floor()?->value()
            && $this->door?->value() === $other->door()?->value()
            && $this->other?->value() === $other->other()?->value();
    }
}