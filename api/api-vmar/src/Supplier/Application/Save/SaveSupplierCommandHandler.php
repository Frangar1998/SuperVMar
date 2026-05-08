<?php

namespace SuperVMar\Supplier\Application\Save;

use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;
use SuperVMar\Supplier\Domain\ValueObject\Contact;
use SuperVMar\Supplier\Domain\ValueObject\Email;
use SuperVMar\Supplier\Domain\ValueObject\Phone;

final readonly class SaveSupplierCommandHandler implements CommandHandler
{
    public function __construct(
        private SupplierCreator $supplierCreator,
        private SupplierUpdater $supplierUpdater,
    )
    {
    }

    public function __invoke(SaveSupplierCommand $command): void
    {
        $id = new Id($command->id());
        $name = new Name($command->name());
        $phone = new Phone($command->phone());
        $email = new Email($command->email());
        $contact = new Contact($command->contact());

        try {
            $this->supplierUpdater->update(
                $id,
                $name,
                $phone,
                $email,
                $contact
            );
        } catch (ItemNotFoundException) {
            $this->supplierCreator->create(
                $id,
                $name,
                $phone,
                $email,
                $contact
            );
        }
    }
}