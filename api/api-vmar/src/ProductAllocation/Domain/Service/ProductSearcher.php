<?php

namespace SuperVMar\ProductAllocation\Domain\Service;

use SuperVMar\ProductAllocation\Domain\Entity\Product;
use SuperVMar\ProductAllocation\Domain\ProductRepository;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class ProductSearcher
{
    public function __construct(
        private ProductRepository $productRepository,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function search(Id $idProduct): Product
    {
        return $this->productRepository->searchById($idProduct);
    }
}

