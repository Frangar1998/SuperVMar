<?php

namespace SuperVMar\Product\Application\Delete;

use SuperVMar\Product\Domain\ProductRepository;
use SuperVMar\Product\Domain\Service\ProductSearcher;
use SuperVMar\Shared\Domain\ValueObject\Id;

readonly class ProductDeleter
{
    public function __construct(
        private ProductSearcher   $productSearcher,
        private ProductRepository $productRepository,
    )
    {
    }

    public function delete(
        Id $id
    ): void
    {
        $product = $this->productSearcher->search($id);
        $product->checkIfCanDelete($this->productRepository);
        $this->productRepository->delete($product->id());
    }
}