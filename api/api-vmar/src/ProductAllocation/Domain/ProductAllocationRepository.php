<?php

namespace SuperVMar\ProductAllocation\Domain;

use SuperVMar\ProductAllocation\Domain\ValueObject\Quantity;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

interface ProductAllocationRepository
{
    public function insert(ProductAllocation $productAllocation): void;
    public function update(ProductAllocation $productAllocation): void;
    public function updateQuantity(ProductAllocation $productAllocation): void;
    public function delete(Id $idSpace): void;
    /**
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): ProductsAllocations;
}