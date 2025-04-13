<?php

namespace SuperVMar\ProductAllocation\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class DeleteProductAllocationCommandHandler implements CommandHandler
{
    public function __construct(
        private ProductAllocationDeleter $productAllocationDeleter,
    )
    {
    }

    public function __invoke(DeleteProductAllocationCommand $command): void
    {
        $idSpace = new Id($command->idSpace());

        $this->productAllocationDeleter->delete($idSpace);
    }
}