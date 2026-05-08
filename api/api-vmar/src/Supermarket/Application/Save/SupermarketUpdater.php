<?php

namespace SuperVMar\Supermarket\Application\Save;

use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;
use SuperVMar\Supermarket\Domain\Entity\Address;
use SuperVMar\Supermarket\Domain\Entity\Zones;
use SuperVMar\Supermarket\Domain\Service\SupermarketSearcher;
use SuperVMar\Supermarket\Domain\SupermarketRepository;
use SuperVMar\Supermarket\Domain\ValueObject\Email;
use SuperVMar\Supermarket\Domain\ValueObject\Phone;

readonly class SupermarketUpdater
{
    public function __construct(
        private SupermarketSearcher $supermarketSearcher,
        private SupermarketRepository $supermarketRepository
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function update(
        Id      $id,
        Name    $name,
        Address $address,
        Phone   $phone,
        Email   $email,
        Zones   $zones
    ): void
    {
        $supermarket = $this->supermarketSearcher->search($id);
        $supermarket->changeName($name);
        $supermarket->changePhone($phone);
        $supermarket->changeEmail($email);
        $supermarket->movedToAddress($address);
        $supermarket->compareAndChangeZones($zones);
        $this->supermarketRepository->update($supermarket);
    }
}