<?php

namespace SuperVMar\Sale\Infrastructure\Dao;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\Sale\Domain\Entity\Line;
use SuperVMar\Sale\Domain\Entity\Lines;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\Field;
use SuperVMar\Shared\Domain\Criteria\FieldName;
use SuperVMar\Shared\Domain\Criteria\Fields;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\FilterField;
use SuperVMar\Shared\Domain\Criteria\FilterOperator;
use SuperVMar\Shared\Domain\Criteria\Filters;
use SuperVMar\Shared\Domain\Criteria\FilterValue;
use SuperVMar\Shared\Domain\Criteria\Join;
use SuperVMar\Shared\Domain\Criteria\JoinFirstTable;
use SuperVMar\Shared\Domain\Criteria\Joins;
use SuperVMar\Shared\Domain\Criteria\JoinSecondTable;
use SuperVMar\Shared\Domain\Criteria\JoinType;
use SuperVMar\Shared\Domain\Criteria\On;
use SuperVMar\Shared\Domain\Criteria\OnFirstField;
use SuperVMar\Shared\Domain\Criteria\OnOperator;
use SuperVMar\Shared\Domain\Criteria\OnSecondField;
use SuperVMar\Shared\Domain\Criteria\Order;
use SuperVMar\Shared\Domain\Criteria\OrderBy;
use SuperVMar\Shared\Domain\Criteria\Select;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use Throwable;

final readonly class DbalLineDao
{
    private const string TABLE_SALE_LINE = TableNames::TABLE_SALE_LINE->value;

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
    public function insert(Lines $lines, Id $idSale): void
    {
        /**
         * @var Line $line
         */
        foreach ($lines as $line) {
            try {
                $this->connection->createQueryBuilder()
                    ->insert(self::TABLE_SALE_LINE)
                    ->values(
                        [
                            'id' => ':id',
                            'idSale' => ':idSale',
                            'idProduct' => ':idProduct',
                            'amount' => ':amount',
                            'quantity' => ':quantity'
                        ])
                    ->setParameters(
                        [
                            'id' => $line->id(),
                            'idSale' => $idSale,
                            'idProduct' => $line->product()->id(),
                            'amount' => $line->amount(),
                            'quantity' => $line->quantity(),
                        ])
                    ->executeStatement();

            } catch (UniqueConstraintViolationException) {
                throw new DuplicateItemException(Line::class, $line->id());
            } catch (Throwable $e) {
                throw new InternalErrorException($e->getMessage(), $e);
            }

        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(Lines $lines, Id $idSale): void
    {
        /**
         * @var Line $line
         */
        try {
            $this->insert(new Lines($lines->addedItems()), $idSale);
            $this->delete(new Lines($lines->removedItems()));

            foreach ($lines->replacedItems() as $line) {
                $this->connection->createQueryBuilder()
                    ->update(self::TABLE_SALE_LINE)
                    ->set('amount', ':amount')
                    ->set('quantity', ':quantity')
                    ->where('id = :id')
                    ->setParameters(
                        [
                            'id' => $line->id(),
                            'amount' => $line->amount(),
                            'quantity' => $line->quantity(),
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
    public function deleteAll(Id $idSale): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_SALE_LINE)
                ->where('idSale = :idSale')
                ->setParameter('idSale', $idSale)
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function delete(Lines $lines): void
    {
        try {
            /**
             * @var Line $line
             */
            foreach ($lines as $line) {
                $this->connection->createQueryBuilder()
                    ->delete(self::TABLE_SALE_LINE)
                    ->where('id = :id')
                    ->setParameter('id', $line->id())
                    ->executeStatement();
            }

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function search(Id $idSale): array
    {
        $criteria = new Criteria(
            filters: new Filters(
                [
                    new Filter(
                        new FilterField(TableNames::TABLE_SALE_LINE, new FieldName('idSale')),
                        FilterOperator::EQUAL,
                        new FilterValue($idSale)
                    )
                ]
            ),
            select: new Select(
                new Fields([
                    new Field(TableNames::TABLE_SALE_LINE, new FieldName('id')),
                    new Field(TableNames::TABLE_SALE_LINE, new FieldName('amount')),
                    new Field(TableNames::TABLE_SALE_LINE, new FieldName('quantity')),
                    new Field(TableNames::TABLE_PRODUCT, new FieldName('id as idProduct')),
                    new Field(TableNames::TABLE_PRODUCT, new FieldName('name as nameProduct')),
                    new Field(TableNames::TABLE_PRODUCT, new FieldName('price')),
                    new Field(TableNames::TABLE_PRODUCT, new FieldName('ean')),
                    new Field(TableNames::TABLE_TAX, new FieldName('id AS idTax')),
                    new Field(TableNames::TABLE_TAX, new FieldName('name AS nameTax')),
                    new Field(TableNames::TABLE_TAX, new FieldName('percent')),
                ])
            ),
            joins: new Joins(
                [
                    new Join(
                        JoinType::INNER,
                        new JoinFirstTable(TableNames::TABLE_SALE_LINE->value),
                        new JoinSecondTable(TableNames::TABLE_PRODUCT->value),
                        new On(
                            new OnFirstField(TableNames::TABLE_SALE_LINE, new FieldName('idProduct')),
                            OnOperator::EQUAL,
                            new OnSecondField(TableNames::TABLE_PRODUCT, new FieldName('id'))
                        )
                    ),
                    new Join(
                        JoinType::INNER,
                        new JoinFirstTable(TableNames::TABLE_PRODUCT->value),
                        new JoinSecondTable(TableNames::TABLE_TAX->value),
                        new On(
                            new OnFirstField(TableNames::TABLE_PRODUCT, new FieldName('idTax')),
                            OnOperator::EQUAL,
                            new OnSecondField(TableNames::TABLE_TAX, new FieldName('id'))
                        )
                    ),
                ]
            )
        );
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $lines = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$lines) {
            return [];
        }

        return $lines;
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE_SALE_LINE,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}