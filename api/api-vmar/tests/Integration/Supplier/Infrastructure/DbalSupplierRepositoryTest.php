<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Integration\Supplier\Infrastructure;

use SuperVMar\App\Tests\Fixtures\IntegrationFixtures;
use SuperVMar\App\Tests\Integration\Shared\Infrastructure\DbalTestCase;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\FieldName;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\FilterField;
use SuperVMar\Shared\Domain\Criteria\FilterOperator;
use SuperVMar\Shared\Domain\Criteria\FilterValue;
use SuperVMar\Shared\Domain\Criteria\Filters;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Supplier\Domain\Supplier;
use SuperVMar\Supplier\Domain\SupplierRepository;
use SuperVMar\Supplier\Infrastructure\DbalSupplierRepository;

final class DbalSupplierRepositoryTest extends DbalTestCase
{
    private const string SUP_ID      = 'b0000000-0000-0000-0000-000000000004';
    private const string SUP_NAME    = 'Proveedor Integración';
    private const string SUP_PHONE   = '611111111';
    private const string SUP_EMAIL   = 'integration@supplier.com';
    private const string SUP_CONTACT = 'Contacto Int';

    private SupplierRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(DbalSupplierRepository::class);
    }


    public function test_insert_persists_supplier_row(): void
    {
        $this->repository->insert($this->buildSupplier());

        $row = $this->findById('supplier', self::SUP_ID);
        $this->assertNotFalse($row);
        $this->assertSame(self::SUP_NAME, $row['name']);
        $this->assertSame(self::SUP_PHONE, $row['phone']);
        $this->assertSame(self::SUP_EMAIL, $row['email']);
    }

    public function test_insert_throws_on_duplicate_id(): void
    {
        $supplier = $this->buildSupplier();
        $this->repository->insert($supplier);

        $this->expectException(DuplicateItemException::class);
        $this->repository->insert($supplier);
    }


    public function test_update_changes_supplier_name(): void
    {
        $this->repository->insert($this->buildSupplier());
        $this->repository->update($this->buildSupplier(name: 'Proveedor Nuevo'));

        $row = $this->findById('supplier', self::SUP_ID);
        $this->assertSame('Proveedor Nuevo', $row['name']);
    }


    public function test_delete_removes_supplier_row(): void
    {
        $this->repository->insert($this->buildSupplier());
        $this->repository->delete(new Id(self::SUP_ID));

        $row = $this->findById('supplier', self::SUP_ID);
        $this->assertFalse($row);
    }


    public function test_search_returns_matching_supplier(): void
    {
        $this->repository->insert($this->buildSupplier());

        $criteria = new Criteria(new Filters([new Filter(
            new FilterField(TableNames::TABLE_SUPPLIER, new FieldName('id')),
            FilterOperator::EQUAL,
            new FilterValue(self::SUP_ID)
        )]));
        $suppliers = $this->repository->searchByCriteria($criteria);

        $this->assertCount(1, $suppliers);
    }

    public function test_search_throws_when_no_match(): void
    {
        $criteria = new Criteria(new Filters([new Filter(
            new FilterField(TableNames::TABLE_SUPPLIER, new FieldName('id')),
            FilterOperator::EQUAL,
            new FilterValue(self::SUP_ID)
        )]));

        $this->expectException(ItemNotFoundException::class);
        $this->repository->searchByCriteria($criteria);
    }


    private function buildSupplier(string $name = self::SUP_NAME): Supplier
    {
        return Supplier::fromArray([
            'id'      => self::SUP_ID,
            'name'    => $name,
            'phone'   => self::SUP_PHONE,
            'email'   => self::SUP_EMAIL,
            'contact' => self::SUP_CONTACT,
        ]);
    }
}
