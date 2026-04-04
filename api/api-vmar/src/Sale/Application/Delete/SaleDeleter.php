<?php

namespace SuperVMar\Sale\Application\Delete;

use SuperVMar\Sale\Domain\SaleRepository;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class SaleDeleter
{
    public function __construct(
        private SaleRepository $saleRepository,
    )
    {
    }

    public function delete(
        Id $id
    ): void
    {
        $this->saleRepository->delete($id);
    }
}
