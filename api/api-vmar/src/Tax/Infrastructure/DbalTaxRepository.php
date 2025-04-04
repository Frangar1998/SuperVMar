<?php

namespace SuperVMar\Tax\Infrastructure;

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
use SuperVMar\Tax\Domain\Tax;
use SuperVMar\Tax\Domain\Taxes;
use SuperVMar\Tax\Domain\TaxRepository;
use SuperVMar\Tax\Infrastructure\Dao\DbalProductDao;
use Throwable;

final readonly class DbalTaxRepository implements TaxRepository
{
    private const string TABLE_TAX = TableNames::TABLE_TAX->value;

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
    public function insert(Tax $tax): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->insert(self::TABLE_TAX)
                ->values(
                    [
                        'id' => ':id',
                        'name' => ':name',
                        'percent' => ':percent',
                    ])
                ->setParameters(
                    [
                        'id' => $tax->id(),
                        'name' => $tax->name(),
                        'percent' => $tax->percent(),
                    ])
                ->executeStatement();

        } catch (UniqueConstraintViolationException) {
            throw new DuplicateItemException(Tax::class, $tax->id());
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(Tax $tax): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE_TAX)
                ->set('name', ':name')
                ->set('percent', ':percent')
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $tax->id(),
                        'name' => $tax->name(),
                        'percent' => $tax->percent(),
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function delete(Id $idTax): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_TAX)
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $idTax,
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): Taxes
    {
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $taxes = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$taxes) {
            throw new ItemNotFoundException(Tax::class, $criteria->filters()?->toArray());
        }

        return Taxes::fromArray($taxes);
    }

    /**
     * @throws InternalErrorException
     * @throws ItemNotFoundException
     */
    public function checkTaxedProductsExists(Id $idTax): void
    {
        $this->dbalProductDao->checkTaxedProductsExists($idTax);
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE_TAX,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}