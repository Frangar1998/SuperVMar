<?php

namespace SuperVMar\Tax\Application\Save;

use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;
use SuperVMar\Tax\Domain\Tax;
use SuperVMar\Tax\Domain\TaxRepository;
use SuperVMar\Tax\Domain\ValueObject\Percent;

readonly class TaxCreator
{
    public function __construct(
        private TaxRepository $taxRepository,
    )
    {
    }

    public function create(
        Id      $id,
        Name    $name,
        Percent $percent
    ): void
    {
        $this->taxRepository->insert(
            Tax::create(
                $id,
                $name,
                $percent
            )
        );

    }
}