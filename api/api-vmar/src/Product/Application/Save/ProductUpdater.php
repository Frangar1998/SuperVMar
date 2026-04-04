<?php

namespace SuperVMar\Product\Application\Save;

use SuperVMar\Product\Domain\Entity\Category;
use SuperVMar\Product\Domain\Entity\PriceHistory;
use SuperVMar\Product\Domain\Entity\Supplier;
use SuperVMar\Product\Domain\Entity\Tax;
use SuperVMar\Product\Domain\ProductRepository;
use SuperVMar\Product\Domain\Service\ProductSearcher;
use SuperVMar\Product\Domain\ValueObject\Active;
use SuperVMar\Product\Domain\ValueObject\Ean;
use SuperVMar\Product\Domain\ValueObject\Image;
use SuperVMar\Product\Domain\ValueObject\Price;
use SuperVMar\Product\Domain\ValueObject\Stock;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;

final readonly class ProductUpdater
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
    public function update(
        Id           $id,
        Name         $name,
        Price        $price,
        Stock        $stock,
        Tax          $tax,
        Category     $category,
        Active       $active,
        ?Image       $image = null
    ): void
    {
        $product = $this->productSearcher->search($id);
        $product->changeName($name);
        $product->changePrice($price);
        $product->changeStock($stock);
        $product->changeTax($tax);
        $product->changeCategory($category);
        $product->changeStatus($active);
        $product->changeImage($image);
        $this->productRepository->update($product);
    }
}