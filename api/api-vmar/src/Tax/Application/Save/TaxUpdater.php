<?php

namespace SuperVMar\Tax\Application\Save;

use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;
use SuperVMar\Tax\Domain\Service\TaxSearcher;
use SuperVMar\Tax\Domain\TaxRepository;
use SuperVMar\Tax\Domain\ValueObject\Percent;

readonly class TaxUpdater
{
    public function __construct(
        private TaxSearcher $taxSearcher,
        private TaxRepository $taxRepository
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function update(
        Id      $id,
        Name    $name,
        Percent $percent
    ): void
    {
        $tax = $this->taxSearcher->search($id);
        $tax->changeName($name);
        $tax->changePercent($percent);
        $this->taxRepository->update($tax);
    }
}