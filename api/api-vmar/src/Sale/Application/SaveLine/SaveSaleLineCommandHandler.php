<?php

namespace SuperVMar\Sale\Application\SaveLine;

use SuperVMar\Sale\Domain\Entity\Product;
use SuperVMar\Sale\Domain\ValueObject\Quantity;
use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class SaveSaleLineCommandHandler implements CommandHandler
{
    public function __construct(
        private SaleCreator $saleCreator,
        private SaleUpdater $saleUpdater,
    )
    {
    }

    public function __invoke(SaveSaleLineCommand $command): void
    {
        $id = new Id($command->id());
        $product = Product::fromPrimitives(
            $command->product()['id'],
            $command->product()['name'],
            $command->product()['price'],
            $command->product()['ean'],
            $command->product()['tax']
        );
        $quantity = new Quantity($command->quantity());

        try {
            $this->saleUpdater->update(
                $id,
                $product,
                $quantity
            );
        } catch (ItemNotFoundException) {
            $this->saleCreator->create(
                $id,
                $product,
                $quantity
            );
        }
    }
}