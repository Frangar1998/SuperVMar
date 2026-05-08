<?php

namespace SuperVMar\Product\Application\ReceiveStock;

use SuperVMar\Product\Domain\ProductRepository;
use SuperVMar\Product\Domain\Service\ProductSearcher;
use SuperVMar\Product\Domain\ValueObject\Stock;
use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class ReceiveStockCommandHandler implements CommandHandler
{
    public function __construct(
        private ProductSearcher $productSearcher,
        private ProductRepository $productRepository,
    )
    {
    }

    public function __invoke(ReceiveStockCommand $command): void
    {
        $idProduct = new Id($command->idProduct());
        $stock = new Stock($command->quantity());

        $product = $this->productSearcher->search($idProduct);
        $product->addStock($stock);
        $this->productRepository->updateStock($idProduct, $product->stock());
    }
}
