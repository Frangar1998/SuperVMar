<?php

namespace SuperVMar\Sale\Domain;

use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

interface SaleRepository
{
    public function insert(Sale $sale): void;
    public function update(Sale $sale): void;
    public function delete(Id $id): void;
    /**
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): Sales;
}