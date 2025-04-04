<?php

namespace SuperVMar\Supplier\Application\Save;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class SaveSupplierCommand implements Command
{
    public function __construct(
        private string $id,
        private string $name,
        private string $phone,
        private string $email,
        private string $contact
    )
    {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function phone(): string
    {
        return $this->phone;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function contact(): string
    {
        return $this->contact;
    }
}