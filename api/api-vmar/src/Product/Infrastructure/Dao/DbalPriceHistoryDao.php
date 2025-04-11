<?php

namespace SuperVMar\Product\Infrastructure\Dao;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\Product\Domain\Entity\HistoricalPrice;
use SuperVMar\Product\Domain\Entity\PriceHistory;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\FieldName;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\FilterField;
use SuperVMar\Shared\Domain\Criteria\FilterOperator;
use SuperVMar\Shared\Domain\Criteria\Filters;
use SuperVMar\Shared\Domain\Criteria\FilterValue;
use SuperVMar\Shared\Domain\Criteria\Order;
use SuperVMar\Shared\Domain\Criteria\OrderBy;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use Throwable;

final readonly class DbalPriceHistoryDao
{
    private const string TABLE_PRICE_HISTORY = TableNames::TABLE_PRICE_HISTORY->value;

    public function __construct(
        private Connection $connection,
        private DbalCriteriaConverter $dbalCriteriaConverter
    )
    {
    }

    /**
     * @throws DuplicateItemException
     * @throws InternalErrorException
     */
    public function insert(PriceHistory $priceHistory, Id $idProduct): void
    {
        /**
         * @var HistoricalPrice $historicalPrice
         */
        foreach ($priceHistory as $historicalPrice) {
            try {
                $this->connection->createQueryBuilder()
                    ->insert(self::TABLE_PRICE_HISTORY)
                    ->values(
                        [
                            'id' => ':id',
                            'idProduct' => ':idProduct',
                            'price' => ':price',
                            'startDate' => ':startDate'
                        ])
                    ->setParameters(
                        [
                            'id' => $historicalPrice->id(),
                            'idProduct' => $idProduct,
                            'price' => $historicalPrice->price(),
                            'startDate' => $historicalPrice->startDate()
                        ])
                    ->executeStatement();

            } catch (UniqueConstraintViolationException) {
                throw new DuplicateItemException(HistoricalPrice::class, $historicalPrice->id());
            } catch (Throwable $e) {
                throw new InternalErrorException($e->getMessage(), $e);
            }

        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(PriceHistory $priceHistory, Id $idProduct): void
    {
        /**
         * @var HistoricalPrice $historicalPrice
         */
        try {
            $this->insert(new PriceHistory($priceHistory->addedItems()), $idProduct);

            foreach ($priceHistory->replacedItems() as $historicalPrice) {
                $this->connection->createQueryBuilder()
                    ->update(self::TABLE_PRICE_HISTORY)
                    ->set('endDate', ':endDate')
                    ->where('id = :id')
                    ->setParameters(
                        [
                            'id' => $historicalPrice->id(),
                            'endDate' => $historicalPrice->endDate(),
                        ])
                    ->executeStatement();

            }
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function deleteAll(Id $idProduct): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_PRICE_HISTORY)
                ->where('idProduct = :idProduct')
                ->setParameter('idProduct', $idProduct)
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function search(Id $idProduct): array
    {
        $criteria = new Criteria(
            filters: new Filters(
                [
                    new Filter(
                        new FilterField(TableNames::TABLE_PRICE_HISTORY, new FieldName('idProduct')),
                        FilterOperator::EQUAL,
                        new FilterValue($idProduct)
                    )
                ]
            ),
            order: Order::createDesc(new OrderBy('startDate'))
        );
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $priceHistory = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$priceHistory) {
            return [];
        }

        return $priceHistory;
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE_PRICE_HISTORY,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}