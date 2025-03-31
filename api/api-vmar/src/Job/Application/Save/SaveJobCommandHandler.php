<?php

namespace SuperVMar\Job\Application\Save;

use SuperVMar\Job\Domain\ValueObject\Name;
use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class SaveJobCommandHandler implements CommandHandler
{
    public function __construct(
        private JobCreator $jobCreator,
        private JobUpdater $jobUpdater,
    )
    {
    }

    public function __invoke(SaveJobCommand $command): void
    {
        $id = new Id($command->id());
        $name = new Name($command->name());

        try {
            $this->jobUpdater->update(
                $id,
                $name
            );
        } catch (ItemNotFoundException) {
            $this->jobCreator->create(
                $id,
                $name
            );
        }
    }
}