<?php

namespace SuperVMar\Category\Domain;

use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

interface CategoryRepository
{
    public function insert(Category $category): void;
    public function update(Category $category): void;
    public function delete(Id $idCategory): void;
    /**
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): Categories;
    /**
     * @throws ItemNotFoundException
     */
    public function checkCategorizedProductsExists(Id $idCategory): void;
}