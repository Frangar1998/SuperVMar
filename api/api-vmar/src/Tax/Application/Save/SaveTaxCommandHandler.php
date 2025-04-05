<?php

namespace SuperVMar\Tax\Application\Save;

use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;
use SuperVMar\Tax\Domain\ValueObject\Percent;

final readonly class SaveTaxCommandHandler implements CommandHandler
{
    public function __construct(
        private TaxCreator $taxCreator,
        private TaxUpdater $taxUpdater,
    )
    {
    }

    public function __invoke(SaveTaxCommand $command): void
    {
        $id = new Id($command->id());
        $name = new Name($command->name());
        $percent = new Percent($command->percent());

        try {
            $this->taxUpdater->update(
                $id,
                $name,
                $percent
            );
        } catch (ItemNotFoundException) {
            $this->taxCreator->create(
                $id,
                $name,
                $percent
            );
        }
    }
}