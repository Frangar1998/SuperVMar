<?php

namespace SuperVMar\ProductAllocation\Domain;

use SuperVMar\ProductAllocation\Domain\Entity\Product;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

interface ProductRepository
{
    /**
     * @throws ItemNotFoundException
     */
    public function searchById(Id $idProduct): Product;
}

