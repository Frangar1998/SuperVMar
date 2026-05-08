<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Integration\Supermarket\Infrastructure;

use SuperVMar\App\Tests\Integration\Shared\Infrastructure\DbalTestCase;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;
use SuperVMar\Supermarket\Domain\Entity\Address;
use SuperVMar\Supermarket\Domain\Entity\Zones;
use SuperVMar\Supermarket\Domain\Service\SupermarketSearcher;
use SuperVMar\Supermarket\Domain\Supermarket;
use SuperVMar\Supermarket\Domain\ValueObject\Email;
use SuperVMar\Supermarket\Domain\ValueObject\Phone;
use SuperVMar\Supermarket\Infrastructure\DbalSupermarketRepository;

final class DbalSupermarketRepositoryTest extends DbalTestCase
{
    private const string SM_ID   = 'e0000000-0000-0000-0000-000000000001';
    private const string ADDR_ID = 'e0000000-0000-0000-0000-000000000002';

    private DbalSupermarketRepository $repository;
    private SupermarketSearcher $searcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(DbalSupermarketRepository::class);
        $this->searcher   = self::getContainer()->get(SupermarketSearcher::class);
    }


    private function buildSupermarket(
        string $id = self::SM_ID,
        string $addrId = self::ADDR_ID,
        string $name = 'Supermercado Test',
        string $phone = '600123456',
        string $email = 'sm@test.com',
    ): Supermarket {
        return Supermarket::create(
            new Id($id),
            new Name($name),
            Address::fromArray([
                'id'         => $addrId,
                'name'       => 'Calle Test',
                'postalCode' => '28080',
                'city'       => 'Madrid',
                'number'     => '5',
                'province'   => 'Madrid',
            ]),
            new Phone($phone),
            new Email($email),
            Zones::fromArray([]),
        );
    }

    private function criteriaById(string $id): Criteria
    {
        return new Criteria(
            filters: new Filters([
                new Filter(
                    new FilterField(TableNames::TABLE_SUPERMARKET, new FieldName('id')),
                    FilterOperator::EQUAL,
                    new FilterValue($id),
                ),
            ])
        );
    }


    public function test_insert_persists_supermarket(): void
    {
        $this->repository->insert($this->buildSupermarket());

        $row = $this->findById('supermarket', self::SM_ID);
        $this->assertNotFalse($row);
        $this->assertSame('Supermercado Test', $row['name']);
    }


    public function test_update_changes_name_and_phone(): void
    {
        $this->repository->insert($this->buildSupermarket());

        $updated = $this->buildSupermarket(name: 'Nuevo Nombre', phone: '699999999');
        $this->repository->update($updated);

        $row = $this->findById('supermarket', self::SM_ID);
        $this->assertSame('Nuevo Nombre', $row['name']);
        $this->assertSame('699999999', $row['phone']);
    }


    public function test_delete_removes_supermarket(): void
    {
        $sm = $this->buildSupermarket();
        $this->repository->insert($sm);
        $this->repository->delete($sm);

        $this->assertSame(0, $this->countRows('supermarket', ['id' => self::SM_ID]));
    }


    public function test_search_by_criteria_returns_supermarket(): void
    {
        $this->repository->insert($this->buildSupermarket());

        $found = $this->searcher->search(new Id(self::SM_ID));

        $this->assertInstanceOf(Supermarket::class, $found);
        $this->assertSame(self::SM_ID, $found->id()->value());
    }


    public function test_search_all_by_criteria_returns_supermarkets_collection(): void
    {
        $this->repository->insert($this->buildSupermarket());

        $results = $this->searcher->searchAll();

        $this->assertGreaterThanOrEqual(1, count($results->toArray()));
    }
}
