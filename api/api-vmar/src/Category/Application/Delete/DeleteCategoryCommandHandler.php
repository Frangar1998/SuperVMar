<?php

namespace SuperVMar\Category\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class DeleteCategoryCommandHandler implements CommandHandler
{
    public function __construct(
        private CategoryDeleter $categoryDeleter,
    )
    {
    }

    public function __invoke(DeleteCategoryCommand $command): void
    {
        $id = new Id($command->id());

        $this->categoryDeleter->delete($id);
    }
}