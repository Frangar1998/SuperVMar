<?php

namespace SuperVMar\Tax\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class DeleteTaxCommandHandler implements CommandHandler
{
    public function __construct(
        private TaxDeleter $taxDeleter,
    )
    {
    }

    public function __invoke(DeleteTaxCommand $command): void
    {
        $id = new Id($command->id());

        $this->taxDeleter->delete($id);
    }
}