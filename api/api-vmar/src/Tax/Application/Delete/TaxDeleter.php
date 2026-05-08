<?php

namespace SuperVMar\Tax\Application\Delete;

use SuperVMar\Shared\Domain\Exception\CannotDeleteException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Tax\Domain\Service\TaxSearcher;
use SuperVMar\Tax\Domain\TaxRepository;

readonly class TaxDeleter
{
    public function __construct(
        private TaxSearcher $taxSearcher,
        private TaxRepository $taxRepository,
    )
    {
    }

    public function delete(
        Id $id
    ): void
    {
        try {
            $this->taxSearcher->checkTaxedProductsExists($id);
            throw new CannotDeleteException("Cannot delete a tax with existing taxed products.");
        } catch (ItemNotFoundException) {
            $this->taxRepository->delete($id);
        }
    }
}