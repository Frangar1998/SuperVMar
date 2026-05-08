<?php

namespace SuperVMar\Supermarket\Application\Save;

use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;
use SuperVMar\Supermarket\Domain\Entity\Address;
use SuperVMar\Supermarket\Domain\Entity\Zones;
use SuperVMar\Supermarket\Domain\Supermarket;
use SuperVMar\Supermarket\Domain\SupermarketRepository;
use SuperVMar\Supermarket\Domain\ValueObject\Email;
use SuperVMar\Supermarket\Domain\ValueObject\Phone;

readonly class SupermarketCreator
{
    public function __construct(
        private SupermarketRepository $supermarketRepository,
    )
    {
    }

    public function create(
        Id      $id,
        Name    $name,
        Address $address,
        Phone   $phone,
        Email   $email,
        Zones   $zones
    ): void
    {
        $this->supermarketRepository->insert(
            Supermarket::create(
                $id,
                $name,
                $address,
                $phone,
                $email,
                $zones
            )
        );

    }
}