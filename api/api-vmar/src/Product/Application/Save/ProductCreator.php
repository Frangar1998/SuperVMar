<?php

namespace SuperVMar\Product\Application\Save;

use SuperVMar\Product\Domain\Entity\Category;
use SuperVMar\Product\Domain\Entity\PriceHistory;
use SuperVMar\Product\Domain\Entity\Supplier;
use SuperVMar\Product\Domain\Entity\Tax;
use SuperVMar\Product\Domain\Product;
use SuperVMar\Product\Domain\ProductRepository;
use SuperVMar\Product\Domain\ValueObject\Active;
use SuperVMar\Product\Domain\ValueObject\Ean;
use SuperVMar\Product\Domain\ValueObject\Image;
use SuperVMar\Product\Domain\ValueObject\Price;
use SuperVMar\Product\Domain\ValueObject\Stock;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;

readonly class ProductCreator
{
    public function __construct(
        private ProductRepository $productRepository,
    )
    {
    }

    public function create(
        Id           $id,
        Name         $name,
        Price        $price,
        Ean          $ean,
        Stock        $stock,
        Tax          $tax,
        Category     $category,
        Supplier     $supplier,
        Active       $active,
        ?Image       $image = null
    ): void
    {
        $this->productRepository->insert(
            Product::create(
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
            )
        );

    }
}