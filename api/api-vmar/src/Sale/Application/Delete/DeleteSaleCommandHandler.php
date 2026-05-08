<?php

namespace SuperVMar\Sale\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class DeleteSaleCommandHandler implements CommandHandler
{
    public function __construct(
        private SaleDeleter $saleDeleter,
    )
    {
    }

    public function __invoke(DeleteSaleCommand $command): void
    {
        $id = new Id($command->id());

        $this->saleDeleter->delete($id);
    }
}
