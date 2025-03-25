<?php

namespace SuperVMar\Supermarket\Application\Save;

use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Supermarket\Domain\Entity\Address;
use SuperVMar\Supermarket\Domain\Entity\Zones;
use SuperVMar\Supermarket\Domain\ValueObject\Id;
use SuperVMar\Supermarket\Domain\ValueObject\Name;
use SuperVMar\Supermarket\Domain\ValueObject\Phone;

final readonly class SaveSupermarketCommandHandler implements CommandHandler
{
    public function __construct(
        private SupermarketCreator $supermarketCreator,
        private SupermarketUpdater $supermarketUpdater,
    )
    {
    }

    public function __invoke(SaveSupermarketCommand $command): void
    {
        $id = new Id($command->id());
        $name = new Name($command->name());
        $address = Address::fromArray($command->address());
        $phone = new Phone($command->phone());
        $zones = Zones::fromArray($command->zones());

        try {
            $this->supermarketUpdater->update(
                $id,
                $name,
                $address,
                $phone,
                $zones
            );
        } catch (ItemNotFoundException) {
            $this->supermarketCreator->create(
                $id,
                $name,
                $address,
                $phone,
                $zones
            );
        }
    }
}