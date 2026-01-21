<?php

namespace SuperVMar\Product\Application\Save;

use SuperVMar\Product\Domain\Entity\Category;
use SuperVMar\Product\Domain\Entity\PriceHistory;
use SuperVMar\Product\Domain\Entity\Supplier;
use SuperVMar\Product\Domain\Entity\Tax;
use SuperVMar\Product\Domain\ValueObject\Active;
use SuperVMar\Product\Domain\ValueObject\Ean;
use SuperVMar\Product\Domain\ValueObject\Image;
use SuperVMar\Product\Domain\ValueObject\Price;
use SuperVMar\Product\Domain\ValueObject\Stock;
use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;

final readonly class SaveProductCommandHandler implements CommandHandler
{
    public function __construct(
        private ProductCreator $productCreator,
        private ProductUpdater $productUpdater,
    )
    {
    }

    public function __invoke(SaveProductCommand $command): void
    {
        $id = new Id($command->id());
        $name = new Name($command->name());
        $price = new Price($command->price());
        $stock = new Stock($command->stock());
        $tax = Tax::fromArray($command->tax());
        $category = Category::fromArray($command->category());
        $active = new Active($command->active());
        $image = new Image($command->image());

        try {
            $this->productUpdater->update(
                $id,
                $name,
                $price,
                $stock,
                $tax,
                $category,
                $active,
                $image
            );
        } catch (ItemNotFoundException) {
            $ean = new Ean($command->ean());
            $supplier = Supplier::fromArray($command->supplier());
            $this->productCreator->create(
                $id,
                $name,
                $price,
                $ean,
                $stock,
                $tax,
                $category,
                $supplier,
                $active,
                $image
            );
        }
    }
}