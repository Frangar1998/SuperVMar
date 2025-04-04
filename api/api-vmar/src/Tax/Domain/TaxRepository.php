<?php

namespace SuperVMar\Tax\Domain;

use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

interface TaxRepository
{
    public function insert(Tax $tax): void;
    public function update(Tax $tax): void;
    public function delete(Id $idTax): void;
    /**
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): Taxes;
    /**
     * @throws ItemNotFoundException
     */
    public function checkTaxedProductsExists(Id $idTax): void;
}