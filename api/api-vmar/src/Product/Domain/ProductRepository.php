<?php

namespace SuperVMar\Product\Domain;

use SuperVMar\Product\Domain\ValueObject\Stock;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

interface ProductRepository
{
    public function insert(Product $product): void;
    public function update(Product $product): void;
    public function updateStock(Id $idProduct, Stock $stock): void;
    public function delete(Id $idProduct): void;
    public function checkAllocationExists(Id $idProduct): void;
    /**
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): Products;
}