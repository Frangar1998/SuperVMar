<?php

namespace SuperVMar\Sale\Domain;

use SuperVMar\Sale\Domain\ValueObject\SaleBill;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

interface SaleRepository
{
    public function insert(Sale $sale): void;
    public function update(Sale $sale): void;
    public function updateBill(Id $id, SaleBill $bill): void;
    public function delete(Id $id): void;
    /**
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): Sales;
}