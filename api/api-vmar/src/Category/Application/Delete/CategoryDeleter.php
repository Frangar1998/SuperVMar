<?php

namespace SuperVMar\Category\Application\Delete;

use SuperVMar\Category\Domain\CategoryRepository;
use SuperVMar\Category\Domain\Service\CategorySearcher;
use SuperVMar\Shared\Domain\Exception\CannotDeleteException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

readonly class CategoryDeleter
{
    public function __construct(
        private CategorySearcher $categorySearcher,
        private CategoryRepository $categoryRepository,
    )
    {
    }

    public function delete(
        Id $id
    ): void
    {
        try {
            $this->categorySearcher->checkCategorizedProductsExists($id);
            throw new CannotDeleteException("Cannot delete a category with existing categorized products.");
        } catch (ItemNotFoundException) {
            $this->categoryRepository->delete($id);
        }
    }
}