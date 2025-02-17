<?php

namespace SuperVMar\Supermarket\Domain;

use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Supermarket\Domain\ValueObject\Id;

interface SupermarketRepository
{
    public function save(Supermarket $supermarket): void;

    public function search(Id $id): ?Supermarket;

    public function searchByCriteria(Criteria $criteria): ?Supermarket;
}