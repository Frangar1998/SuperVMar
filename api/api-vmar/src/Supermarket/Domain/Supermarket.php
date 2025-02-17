<?php

namespace SuperVMar\Supermarket\Domain;

use SuperVMar\Shared\Domain\AggregateRoot;
use SuperVMar\Supermarket\Domain\Entity\Address;
use SuperVMar\Supermarket\Domain\Entity\Zone;
use SuperVMar\Supermarket\Domain\Entity\Zones;
use SuperVMar\Supermarket\Domain\ValueObject\Id;
use SuperVMar\Supermarket\Domain\ValueObject\Name;
use SuperVMar\Supermarket\Domain\ValueObject\Phone;

final class Supermarket extends AggregateRoot
{
    public function __construct(
        private readonly Id      $id,
        private readonly Name    $name,
        private readonly Address $address,
        private readonly Phone   $phone,
        private readonly Zones   $zones,
    ){}

    public function id(): Id
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function address(): Address
    {
        return $this->address;
    }

    public function phone(): Phone
    {
        return $this->phone;
    }

    public function zones(): Zones
    {
        return $this->zones;
    }

    public static function create(
        Id $id,
        Name $name,
        Address $address,
        Phone $phone,
        Zones $zones
    ): self
    {
        return new self(
            $id,
            $name,
            $address,
            $phone,
            $zones
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Name($data['name']),
            Address::fromArray($data['address']),
            new Phone($data['phone']),
            new Zones(
                array_map(
                    fn(array $zone) => Zone::fromArray($zone),
                    $data['zones']
                )
            )
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'address' => $this->address->toArray(),
            'phone' => $this->phone->value(),
            'zones' => $this->zones->toArray()
        ];
    }
}