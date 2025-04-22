<?php

namespace SuperVMar\Supermarket\Infrastructure\Dao;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use JsonException;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\FieldName;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\FilterField;
use SuperVMar\Shared\Domain\Criteria\FilterOperator;
use SuperVMar\Shared\Domain\Criteria\Filters;
use SuperVMar\Shared\Domain\Criteria\FilterValue;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\Utils;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use SuperVMar\Supermarket\Domain\Entity\Space;
use SuperVMar\Supermarket\Domain\Entity\Spaces;
use Throwable;

final readonly class DbalSpaceDao
{
    private const string TABLE_SPACE = TableNames::TABLE_SPACE->value;

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
    public function insert(Spaces $spaces, Id $idZone): void
    {
        /**
         * @var Space $space
         */
        foreach ($spaces as $space) {
            try {
                $this->connection->createQueryBuilder()
                    ->insert(self::TABLE_SPACE)
                    ->values(
                        [
                            'id' => ':id',
                            'position' => ':position',
                            'idZone' => ':idZone',
                            'maxSpots' => ':maxSpots',
                        ])
                    ->setParameters(
                        [
                            'id' => $space->id(),
                            'position' => $space->position(),
                            'idZone' => $idZone,
                            'maxSpots' => $space->maxSpots()->value(),
                        ])
                    ->executeStatement();

            } catch (UniqueConstraintViolationException) {
                throw new DuplicateItemException(Space::class, $space->id());
            } catch (Throwable $e) {
                throw new InternalErrorException($e->getMessage(), $e);
            }

        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(Spaces $spaces, Id $idZone): void
    {
        /**
         * @var Space $space
         */
        try {
            $this->insert(new Spaces($spaces->addedItems()), $idZone);
            $this->delete(new Spaces($spaces->removedItems()));

            foreach ($spaces->replacedItems() as $space) {
                $this->connection->createQueryBuilder()
                    ->update(self::TABLE_SPACE)
                    ->set('maxSpots', ':maxSpots')
                    ->where('id = :id')
                    ->setParameters(
                        [
                            'id' => $space->id(),
                            'maxSpots' => $space->maxSpots(),
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
    public function delete(Spaces $spaces): void
    {
        try {
            /**
             * @var Space $space
             */
            foreach ($spaces as $space) {
                $this->connection->createQueryBuilder()
                    ->delete(self::TABLE_SPACE)
                    ->where('id = :id')
                    ->setParameter('id', $space->id())
                    ->executeStatement();
            }

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function deleteAll(Id $idZone): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_SPACE)
                ->where('idZone = :idZone')
                ->setParameter('idZone', $idZone)
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException|JsonException
     */
    public function search(Id $idZone): array
    {
        $criteria = new Criteria(
            new Filters(
                [
                    new Filter(
                        new FilterField(TableNames::TABLE_SPACE, new FieldName('idZone')),
                        FilterOperator::EQUAL,
                        new FilterValue($idZone)
                    )
                ]
            )
        );
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $spaces = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$spaces) {
            return [];
        }

        foreach ($spaces as $key => $space) {
            $spaces[$key]['position'] = Utils::jsonDecode($space['position']);
        }

        return $spaces;
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE_SPACE,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}