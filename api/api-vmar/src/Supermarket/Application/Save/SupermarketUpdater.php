<?php

namespace SuperVMar\Supermarket\Application\Save;

use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Supermarket\Domain\Entity\Address;
use SuperVMar\Supermarket\Domain\Entity\Zones;
use SuperVMar\Supermarket\Domain\Service\SupermarketSearcher;
use SuperVMar\Supermarket\Domain\SupermarketRepository;
use SuperVMar\Supermarket\Domain\ValueObject\Id;
use SuperVMar\Supermarket\Domain\ValueObject\Name;
use SuperVMar\Supermarket\Domain\ValueObject\Phone;

final readonly class SupermarketUpdater
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
        Zones   $zones
    ): void
    {
        $supermarket = $this->supermarketSearcher->search($id);
        $supermarket->changeName($name);
        $supermarket->changePhone($phone);
        $supermarket->movedToAddress($address);
        $supermarket->compareAndChangeZones($zones);
        $this->supermarketRepository->update($supermarket);
    }
}