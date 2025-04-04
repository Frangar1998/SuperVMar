<?php

namespace SuperVMar\Supplier\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use SuperVMar\Supplier\Domain\Supplier;
use SuperVMar\Supplier\Domain\SupplierRepository;
use SuperVMar\Supplier\Domain\Suppliers;
use SuperVMar\Supplier\Infrastructure\Dao\DbalProductDao;
use Throwable;

final readonly class DbalSupplierRepository implements SupplierRepository
{
    private const string TABLE_SUPPLIER = TableNames::TABLE_SUPPLIER->value;

    public function __construct(
        private Connection $connection,
        private DbalCriteriaConverter $dbalCriteriaConverter,
        private DbalProductDao $dbalProductDao
    )
    {
    }

    /**
     * @throws DuplicateItemException
     * @throws InternalErrorException
     */
    public function insert(Supplier $supplier): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->insert(self::TABLE_SUPPLIER)
                ->values(
                    [
                        'id' => ':id',
                        'name' => ':name',
                        'phone' => ':phone',
                        'email' => ':email',
                        'contact' => ':contact',
                    ])
                ->setParameters(
                    [
                        'id' => $supplier->id(),
                        'name' => $supplier->name(),
                        'phone' => $supplier->phone(),
                        'email' => $supplier->email(),
                        'contact' => $supplier->contact(),
                    ])
                ->executeStatement();

        } catch (UniqueConstraintViolationException) {
            throw new DuplicateItemException(Supplier::class, $supplier->id());
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(Supplier $supplier): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE_SUPPLIER)
                ->set('name', ':name')
                ->set('phone', ':phone')
                ->set('email', ':email')
                ->set('contact', ':contact')
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $supplier->id(),
                        'name' => $supplier->name(),
                        'phone' => $supplier->phone(),
                        'email' => $supplier->email(),
                        'contact' => $supplier->contact(),
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function delete(Id $idSupplier): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_SUPPLIER)
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $idSupplier,
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    public function searchByCriteria(Criteria $criteria): Suppliers
    {
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $suppliers = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$suppliers) {
            throw new ItemNotFoundException(Supplier::class, $criteria->filters()?->toArray());
        }

        return Suppliers::fromArray($suppliers);
    }

    /**
     * @throws InternalErrorException
     * @throws ItemNotFoundException
     */
    public function checkSuppliedProductsExists(Id $idSupplier): void
    {
        $this->dbalProductDao->checkSuppliedProductsExists($idSupplier);
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE_SUPPLIER,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}