<?php

namespace SuperVMar\Supermarket\Domain\Repository;

use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Supermarket\Domain\Supermarket;
use SuperVMar\Supermarket\Domain\ValueObject\Id;

interface SupermarketRepository
{
    public function insert(Supermarket $supermarket): void;
    public function update(Supermarket $supermarket): void;
    public function delete(Supermarket $supermarket): void;
    /**
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): ?Supermarket;
}