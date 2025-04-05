<?php

namespace SuperVMar\Supplier\Application\Save;

use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;
use SuperVMar\Supplier\Domain\Service\SupplierSearcher;
use SuperVMar\Supplier\Domain\SupplierRepository;
use SuperVMar\Supplier\Domain\ValueObject\Contact;
use SuperVMar\Supplier\Domain\ValueObject\Email;
use SuperVMar\Supplier\Domain\ValueObject\Phone;

final readonly class SupplierUpdater
{
    public function __construct(
        private SupplierSearcher $supplierSearcher,
        private SupplierRepository $supplierRepository
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function update(
        Id      $id,
        Name    $name,
        Phone   $phone,
        Email   $email,
        Contact $contact
    ): void
    {
        $supplier = $this->supplierSearcher->search($id);
        $supplier->changeName($name);
        $supplier->changePhone($phone);
        $supplier->changeEmail($email);
        $supplier->changeContact($contact);
        $this->supplierRepository->update($supplier);
    }
}