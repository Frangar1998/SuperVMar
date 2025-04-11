<?php

namespace SuperVMar\Product\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class DeleteProductCommandHandler implements CommandHandler
{
    public function __construct(
        private ProductDeleter $productDeleter,
    )
    {
    }

    public function __invoke(DeleteProductCommand $command): void
    {
        $id = new Id($command->id());

        $this->productDeleter->delete($id);
    }
}