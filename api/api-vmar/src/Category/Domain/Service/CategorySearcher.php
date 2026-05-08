<?php

namespace SuperVMar\Category\Domain\Service;

use SuperVMar\Category\Domain\Categories;
use SuperVMar\Category\Domain\Category;
use SuperVMar\Category\Domain\CategoryRepository;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\FieldName;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\FilterField;
use SuperVMar\Shared\Domain\Criteria\FilterOperator;
use SuperVMar\Shared\Domain\Criteria\Filters;
use SuperVMar\Shared\Domain\Criteria\FilterValue;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;

readonly class CategorySearcher
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function search(Id $idCategory): Category
    {
        return $this->categoryRepository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_CATEGORY, new FieldName('id')),
                            FilterOperator::EQUAL,
                            new FilterValue($idCategory)
                        )
                    ]
                )
            )
        )->first();
    }

    /**
     * @throws ItemNotFoundException
     */
    public function searchAll(): Categories
    {
        return $this->categoryRepository->searchByCriteria(
            new Criteria()
        );
    }

    /**
     * @throws ItemNotFoundException
     */
    public function checkCategorizedProductsExists(Id $idCategory): void
    {
        $this->categoryRepository->checkCategorizedProductsExists($idCategory);
    }
}