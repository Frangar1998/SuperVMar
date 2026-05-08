<?php

namespace SuperVMar\Supermarket\Application\Save;

use SuperVMar\Shared\Domain\Bus\Command\Command;

final readonly class SaveSupermarketCommand implements Command
{
    public function __construct(
        private string $id,
        private string $name,
        private array $address,
        private string $phone,
        private string $email,
        private array $zones
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

    public function address(): array
    {
        return $this->address;
    }

    public function phone(): string
    {
        return $this->phone;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function zones(): array
    {
        return $this->zones;
    }


}