<?php

namespace SuperVMar\ProductAllocation\Application\Save;

use SuperVMar\ProductAllocation\Domain\Entity\Space;
use SuperVMar\ProductAllocation\Domain\Service\ProductSearcher;
use SuperVMar\ProductAllocation\Domain\ValueObject\Quantity;
use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class SaveProductAllocationCommandHandler implements CommandHandler
{
    public function __construct(
        private ProductAllocationCreator $productAllocationCreator,
        private ProductAllocationUpdater $productAllocationUpdater,
        private ProductSearcher          $productSearcher,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function __invoke(SaveProductAllocationCommand $command): void
    {
        $product = $this->productSearcher->search(new Id($command->product()));
        $space = Space::fromArray(['id' => $command->idSpace()]);
        $quantity = new Quantity($command->quantity());

        try {
            $this->productAllocationUpdater->update(
                $product,
                $space,
                $quantity,
            );
        } catch (ItemNotFoundException) {
            $this->productAllocationCreator->create(
                $product,
                $space,
                $quantity,
            );
        }
    }
}