<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Integration\Tax\Infrastructure;

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
use SuperVMar\Tax\Domain\Tax;
use SuperVMar\Tax\Domain\TaxRepository;
use SuperVMar\Tax\Infrastructure\DbalTaxRepository;

final class DbalTaxRepositoryTest extends DbalTestCase
{
    private const string TAX_ID      = 'b0000000-0000-0000-0000-000000000002';
    private const string TAX_NAME    = 'IVA 10%';
    private const float  TAX_PERCENT = 10.0;

    private TaxRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(DbalTaxRepository::class);
    }


    public function test_insert_persists_tax_row(): void
    {
        $this->repository->insert($this->buildTax());

        $row = $this->findById('tax', self::TAX_ID);
        $this->assertNotFalse($row);
        $this->assertSame(self::TAX_NAME, $row['name']);
        $this->assertEqualsWithDelta(self::TAX_PERCENT, (float) $row['percent'], 0.001);
    }

    public function test_insert_throws_on_duplicate_id(): void
    {
        $tax = $this->buildTax();
        $this->repository->insert($tax);

        $this->expectException(DuplicateItemException::class);
        $this->repository->insert($tax);
    }


    public function test_update_changes_tax_name(): void
    {
        $this->repository->insert($this->buildTax());
        $this->repository->update($this->buildTax(name: 'IVA 10% Reducido'));

        $row = $this->findById('tax', self::TAX_ID);
        $this->assertSame('IVA 10% Reducido', $row['name']);
    }

    public function test_update_changes_tax_percent(): void
    {
        $this->repository->insert($this->buildTax());
        $this->repository->update($this->buildTax(percent: 5.0));

        $row = $this->findById('tax', self::TAX_ID);
        $this->assertEqualsWithDelta(5.0, (float) $row['percent'], 0.001);
    }


    public function test_delete_removes_tax_row(): void
    {
        $this->repository->insert($this->buildTax());
        $this->repository->delete(new Id(self::TAX_ID));

        $row = $this->findById('tax', self::TAX_ID);
        $this->assertFalse($row);
    }


    public function test_search_returns_matching_tax(): void
    {
        $this->repository->insert($this->buildTax());

        $criteria = new Criteria(new Filters([new Filter(
            new FilterField(TableNames::TABLE_TAX, new FieldName('id')),
            FilterOperator::EQUAL,
            new FilterValue(self::TAX_ID)
        )]));
        $taxes = $this->repository->searchByCriteria($criteria);

        $this->assertCount(1, $taxes);
    }

    public function test_search_throws_when_no_match(): void
    {
        $criteria = new Criteria(new Filters([new Filter(
            new FilterField(TableNames::TABLE_TAX, new FieldName('id')),
            FilterOperator::EQUAL,
            new FilterValue(self::TAX_ID)
        )]));

        $this->expectException(ItemNotFoundException::class);
        $this->repository->searchByCriteria($criteria);
    }


    private function buildTax(string $name = self::TAX_NAME, float $percent = self::TAX_PERCENT): Tax
    {
        return Tax::fromArray(['id' => self::TAX_ID, 'name' => $name, 'percent' => $percent]);
    }
}
