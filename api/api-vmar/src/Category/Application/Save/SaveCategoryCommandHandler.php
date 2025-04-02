<?php

namespace SuperVMar\Category\Application\Save;

use SuperVMar\Category\Domain\ValueObject\Name;
use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class SaveCategoryCommandHandler implements CommandHandler
{
    public function __construct(
        private CategoryCreator $categoryCreator,
        private CategoryUpdater $categoryUpdater,
    )
    {
    }

    public function __invoke(SaveCategoryCommand $command): void
    {
        $id = new Id($command->id());
        $name = new Name($command->name());

        try {
            $this->categoryUpdater->update(
                $id,
                $name
            );
        } catch (ItemNotFoundException) {
            $this->categoryCreator->create(
                $id,
                $name
            );
        }
    }
}