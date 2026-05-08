<?php

namespace SuperVMar\Supplier\Application\Save;

use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;
use SuperVMar\Supplier\Domain\Supplier;
use SuperVMar\Supplier\Domain\SupplierRepository;
use SuperVMar\Supplier\Domain\ValueObject\Contact;
use SuperVMar\Supplier\Domain\ValueObject\Email;
use SuperVMar\Supplier\Domain\ValueObject\Phone;

readonly class SupplierCreator
{
    public function __construct(
        private SupplierRepository $supplierRepository,
    )
    {
    }

    public function create(
        Id      $id,
        Name    $name,
        Phone   $phone,
        Email   $email,
        Contact $contact
    ): void
    {
        $this->supplierRepository->insert(
            Supplier::create(
                $id,
                $name,
                $phone,
                $email,
                $contact
            )
        );

    }
}