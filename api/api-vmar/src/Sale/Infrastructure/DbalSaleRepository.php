<?php

namespace SuperVMar\Sale\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\Sale\Domain\Entity\Line;
use SuperVMar\Sale\Domain\Sale;
use SuperVMar\Sale\Domain\SaleRepository;
use SuperVMar\Sale\Domain\Sales;
use SuperVMar\Sale\Domain\ValueObject\SaleBill;
use SuperVMar\Sale\Infrastructure\Dao\DbalLineDao;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use Throwable;

final readonly class DbalSaleRepository implements SaleRepository
{
    private const string TABLE_SALE = TableNames::TABLE_SALE->value;

    public function __construct(
        private Connection $connection,
        private DbalCriteriaConverter $dbalCriteriaConverter,
        private DbalLineDao $dbalLineDao,
    )
    {
    }

    /**
     * @throws DuplicateItemException
     * @throws InternalErrorException
     */
    public function insert(Sale $sale): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->insert(self::TABLE_SALE)
                ->values(
                    [
                        'id' => ':id',
                        'amount' => ':amount',
                        'taxes' => ':taxes',
                        'totalAmount' => ':totalAmount',
                        'payMethod' => ':payMethod',
                        'date' => ':date',
                    ])
                ->setParameters(
                    [
                        'id' => $sale->id(),
                        'amount' => $sale->amount(),
                        'taxes' => $sale->taxesAmount(),
                        'totalAmount' => $sale->totalAmount(),
                        'payMethod' => $sale->payMethod()->value,
                        'date' => $sale->finishedDate()?->formatDate()
                    ])
                ->executeStatement();

            $this->dbalLineDao->insert($sale->lines(), $sale->id());

        } catch (UniqueConstraintViolationException) {
            throw new DuplicateItemException(Sale::class, $sale->id());
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(Sale $sale): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE_SALE)
                ->set('amount', ':amount')
                ->set('taxes', ':taxes')
                ->set('totalAmount', ':totalAmount')
                ->set('payMethod', ':payMethod')
                ->set('date', ':date')
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $sale->id(),
                        'amount' => $sale->amount(),
                        'taxes' => $sale->taxesAmount(),
                        'totalAmount' => $sale->totalAmount(),
                        'payMethod' => $sale->payMethod()->value,
                        'date' => $sale->finishedDate()?->formatDate()
                    ])
                ->executeStatement();

            $this->dbalLineDao->update($sale->lines(), $sale->id());

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function updateBill(Id $id, SaleBill $bill): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE_SALE)
                ->set('bill', ':bill')
                ->where('id = :id')
                ->setParameters([
                    'id' => $id,
                    'bill' => $bill->value(),
                ])
                ->executeStatement();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function delete(Id $id): void
    {
        try {
            $this->dbalLineDao->deleteAll($id);

            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_SALE)
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $id,
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws ItemNotFoundException
     * @throws InternalErrorException
     */
    public function searchByCriteria(Criteria $criteria): Sales
    {
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $sales = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$sales) {
            throw new ItemNotFoundException(Sale::class, $criteria->filters()?->toArray() ?? []);
        }

        foreach ($sales as $key => $sale) {
            $sales[$key]['lines'] = $this->dbalLineDao->search(new Id($sale['id']));
        }

        return Sales::fromArray($sales);
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE_SALE,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}