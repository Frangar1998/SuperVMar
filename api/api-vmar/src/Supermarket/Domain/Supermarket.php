<?php

namespace SuperVMar\Supermarket\Domain;

use SuperVMar\Shared\Domain\AggregateRoot;
use SuperVMar\Supermarket\Domain\Entity\Address;
use SuperVMar\Supermarket\Domain\Entity\Zones;
use SuperVMar\Supermarket\Domain\ValueObject\Id;
use SuperVMar\Supermarket\Domain\ValueObject\Name;
use SuperVMar\Supermarket\Domain\ValueObject\Phone;

final class Supermarket extends AggregateRoot
{
    public function __construct(
        private readonly Id $id,
        private Name        $name,
        private Address     $address,
        private Phone       $phone,
        private Zones       $zones
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
        Id      $id,
        Name    $nameSupermarket,
        Address $address,
        Phone   $phone,
        Zones   $zones
    ): self
    {
        return new self(
            $id,
            $nameSupermarket,
            $address,
            $phone,
            $zones
        );
    }

    public function changeName(Name $name): void
    {
        if (!$this->name->equals($name)) {
            $this->name = $name;
        }
    }

    public function changePhone(Phone $phone): void
    {
        if (!$this->phone->equals($phone)) {
            $this->phone = $phone;
        }
    }

    public function movedToAddress(Address $address): void
    {
        if (!$this->address->compare($address)) {
            $this->address = $address;
        }
    }

    public function compareAndChangeZones(Zones $other): void
    {
        foreach ($this->zones as $zone) {
            if ($other->find($zone) === null) {
                //TODO: Check if space has allocated products before removing zone and its spaces. Throw exception if yes.
                $this->zones->remove($zone);
            }
        }
        foreach ($other as $otherZone) {
            $zoneKey = $this->zones->find($otherZone);
            if ($zoneKey !== null) {
                $this->zones->replace($otherZone, $zoneKey);
            } else {
                $this->zones->add($otherZone);
            }
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Name($data['name']),
            Address::fromArray([
                'id' => $data['idAddress'],
                'name' => $data['nameAddress'],
                'postalCode' => $data['postalCode'],
                'city' => $data['city'],
                'number' => $data['number'],
                'province' => $data['province'],
            ]),
            new Phone($data['phone']),
            Zones::fromArray($data['zones'])
        );
    }
}