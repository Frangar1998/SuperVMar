<?php

namespace SuperVMar\Supplier\Domain;

use SuperVMar\Shared\Domain\AggregateRoot;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Supplier\Domain\ValueObject\Contact;
use SuperVMar\Supplier\Domain\ValueObject\Email;
use SuperVMar\Supplier\Domain\ValueObject\Name;
use SuperVMar\Supplier\Domain\ValueObject\Phone;

final class Supplier extends AggregateRoot
{
    public function __construct(
        private readonly Id $id,
        private Name       $name,
        private Phone      $phone,
        private Email      $email,
        private Contact    $contact,
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

    public function phone(): Phone
    {
        return $this->phone;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function contact(): Contact
    {
        return $this->contact;
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

    public function changeEmail(Email $email): void
    {
        if (!$this->email->equals($email)) {
            $this->email = $email;
        }
    }

    public function changeContact(Contact $contact): void
    {
        if (!$this->contact->equals($contact)) {
            $this->contact = $contact;
        }
    }

    public static function create(
        Id      $id,
        Name    $name,
        Phone   $phone,
        Email   $email,
        Contact $contact
    ): self
    {
        return new self(
            $id,
            $name,
            $phone,
            $email,
            $contact
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Name($data['name']),
            new Phone($data['phone']),
            new Email($data['email']),
            new Contact($data['contact'])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'phone' => $this->phone->value(),
            'email' => $this->email->value(),
            'contact' => $this->contact->value(),
        ];
    }
}