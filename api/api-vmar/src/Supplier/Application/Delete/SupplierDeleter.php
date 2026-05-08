<?php

namespace SuperVMar\Supplier\Application\Delete;

use SuperVMar\Shared\Domain\Exception\CannotDeleteException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Supplier\Domain\Service\SupplierSearcher;
use SuperVMar\Supplier\Domain\SupplierRepository;

readonly class SupplierDeleter
{
    public function __construct(
        private SupplierSearcher $supplierSearcher,
        private SupplierRepository $supplierRepository,
    )
    {
    }

    public function delete(
        Id $id
    ): void
    {
        try {
            $this->supplierSearcher->checkSuppliedProductsExists($id);
            throw new CannotDeleteException("Cannot delete a supplier with existing supplied products.");
        } catch (ItemNotFoundException) {
            $this->supplierRepository->delete($id);
        }
    }
}