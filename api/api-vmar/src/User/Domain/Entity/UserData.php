<?php

namespace SuperVMar\User\Domain\Entity;

use SuperVMar\User\Domain\ValueObject\Email;
use SuperVMar\User\Domain\ValueObject\Id;
use SuperVMar\User\Domain\ValueObject\Name;
use SuperVMar\User\Domain\ValueObject\Phone;
use SuperVMar\User\Domain\ValueObject\Surname;

final readonly class UserData
{
    public function __construct(
        private Id          $id,
        private Name        $name,
        private Surname     $surname,
        private Email       $email,
        private Phone       $phone,
        private Address     $address,
    )
    {
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function surname(): Surname
    {
        return $this->surname;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function phone(): Phone
    {
        return $this->phone;
    }

    public function address(): Address
    {
        return $this->address;
    }

    public static function create(
        Id       $id,
        Name     $name,
        Surname  $surname,
        Email    $email,
        Phone    $phone,
        Address  $address
    ): self
    {
        return new self(
            $id,
            $name,
            $surname,
            $email,
            $phone,
            $address
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Name($data['name']),
            new Surname($data['surname']),
            new Email($data['email']),
            new Phone($data['phone']),
            Address::fromArray([
                'id' => $data['idAddress'],
                'name' => $data['nameAddress'],
                'postalCode' => $data['postalCode'],
                'city' => $data['city'],
                'number' => $data['number'],
                'province' => $data['province'],
                'floor' => $data['floor'],
                'door' => $data['door'],
                'other' => $data['other'],
            ]),

        );
    }

    public static function fromPrimitives(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Name($data['name']),
            new Surname($data['surname']),
            new Email($data['email']),
            new Phone($data['phone']),
            Address::fromArray($data['address']),

        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'surname' => $this->surname->value(),
            'email' => $this->email->value(),
            'phone' => $this->phone->value(),
            'address' => $this->address->toArray()
        ];
    }

    public function compare(self $other): bool
    {
        return $this->name->equals($other->name())
            && $this->surname->equals($other->surname())
            && $this->email->equals($other->email())
            && $this->phone->equals($other->phone())
            && $this->address->compare($other->address());
    }

}