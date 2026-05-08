<?php

namespace SuperVMar\Product\Application\SubtractStock;

use SuperVMar\Product\Domain\ProductRepository;
use SuperVMar\Product\Domain\Service\ProductSearcher;
use SuperVMar\Product\Domain\ValueObject\Stock;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

readonly class ProductStockSubtracter
{
    public function __construct(
        private ProductSearcher   $productSearcher,
        private ProductRepository $productRepository
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function subtractStock(
        Id $idProduct,
        Stock $stock,
    ): void
    {
        $product = $this->productSearcher->search($idProduct);
        $product->subtractStock($stock);
        $this->productRepository->update($product);
    }
}