<?php

namespace SuperVMar\Job\Application\Delete;

use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class DeleteJobCommandHandler implements CommandHandler
{
    public function __construct(
        private JobDeleter $jobDeleter,
    )
    {
    }

    public function __invoke(DeleteJobCommand $command): void
    {
        $id = new Id($command->id());

        $this->jobDeleter->delete($id);
    }
}