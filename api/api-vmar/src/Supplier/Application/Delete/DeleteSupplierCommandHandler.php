<?php

namespace SuperVMar\Supplier\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class DeleteSupplierCommandHandler implements CommandHandler
{
    public function __construct(
        private SupplierDeleter $supplierDeleter,
    )
    {
    }

    public function __invoke(DeleteSupplierCommand $command): void
    {
        $id = new Id($command->id());

        $this->supplierDeleter->delete($id);
    }
}