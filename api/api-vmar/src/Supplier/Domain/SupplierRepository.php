<?php

namespace SuperVMar\Supplier\Domain;

use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

interface SupplierRepository
{
    public function insert(Supplier $supplier): void;
    public function update(Supplier $supplier): void;
    public function delete(Id $idSupplier): void;
    /**
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): Suppliers;
    /**
     * @throws ItemNotFoundException
     */
    public function checkSuppliedProductsExists(Id $idSupplier): void;
}