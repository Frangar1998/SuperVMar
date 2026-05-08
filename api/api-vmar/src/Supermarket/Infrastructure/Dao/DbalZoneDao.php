<?php

namespace SuperVMar\Supermarket\Infrastructure\Dao;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
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
use SuperVMar\Supermarket\Domain\Entity\Zone;
use SuperVMar\Supermarket\Domain\Entity\Zones;
use Throwable;

final readonly class DbalZoneDao
{
    private const string TABLE_ZONE = TableNames::TABLE_ZONE->value;

    public function __construct(
        private Connection $connection,
        private DbalCriteriaConverter $dbalCriteriaConverter,
        private DbalSpaceDao $spaceDao
    )
    {
    }

    /**
     * @throws DuplicateItemException
     * @throws InternalErrorException
     */
    public function insert(Zones $zones, Id $idSupermarket): void
    {
        /**
         * @var Zone $zone
         */
        foreach ($zones as $zone) {
            try {
                $this->connection->createQueryBuilder()
                    ->insert(self::TABLE_ZONE)
                    ->values(
                        [
                            'id' => ':id',
                            'name' => ':name',
                            'idSupermarket' => ':idSupermarket',
                            'cornerTopLeft' => ':cornerTopLeft',
                            'cornerTopRight' => ':cornerTopRight',
                            'cornerBottomRight' => ':cornerBottomRight',
                            'cornerBottomLeft' => ':cornerBottomLeft',
                        ])
                    ->setParameters(
                        [
                            'id' => $zone->id(),
                            'name' => $zone->name(),
                            'idSupermarket' => $idSupermarket,
                            'cornerTopLeft' => $zone->cornerTopLeft(),
                            'cornerTopRight' => $zone->cornerTopRight(),
                            'cornerBottomRight' => $zone->cornerBottomRight(),
                            'cornerBottomLeft' => $zone->cornerBottomLeft(),
                        ])
                    ->executeStatement();

                $this->spaceDao->insert($zone->spaces(), $zone->id());

            } catch (UniqueConstraintViolationException) {
                throw new DuplicateItemException(Zone::class, $zone->id());
            } catch (Throwable $e) {
                throw new InternalErrorException($e->getMessage(), $e);
            }
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(Zones $zones, Id $idSupermarket): void
    {
        /**
         * @var Zone $zone
         */
        try {
            $this->insert(new Zones($zones->addedItems()), $idSupermarket);
            $this->delete(new Zones($zones->removedItems()));

            foreach ($zones->replacedItems() as $zone) {
                $this->connection->createQueryBuilder()
                    ->update(self::TABLE_ZONE)
                    ->set('name', ':name')
                    ->set('cornerTopLeft', ':cornerTopLeft')
                    ->set('cornerTopRight', ':cornerTopRight')
                    ->set('cornerBottomRight', ':cornerBottomRight')
                    ->set('cornerBottomLeft', ':cornerBottomLeft')
                    ->where('id = :id')
                    ->setParameters(
                        [
                            'id' => $zone->id(),
                            'name' => $zone->name(),
                            'cornerTopLeft' => $zone->cornerTopLeft(),
                            'cornerTopRight' => $zone->cornerTopRight(),
                            'cornerBottomRight' => $zone->cornerBottomRight(),
                            'cornerBottomLeft' => $zone->cornerBottomLeft(),
                        ])
                    ->executeStatement();
            }

            foreach ($zones as $zone) {
                $this->spaceDao->update($zone->spaces(), $zone->id());
            }
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function delete(Zones $zones): void
    {
        try {
            /**
             * @var Zone $zone
             */
            foreach ($zones as $zone) {
                $this->spaceDao->delete($zone->spaces());

                $this->connection->createQueryBuilder()
                    ->delete(self::TABLE_ZONE)
                    ->where('id = :id')
                    ->setParameter('id', $zone->id())
                    ->executeStatement();
            }


        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function deleteAll(Zones $zones, Id $idSupermarket): void
    {
        try {
            /**
             * @var Zone $zone
             */
            foreach ($zones as $zone) {
                $this->spaceDao->deleteAll($zone->id());
            }
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_ZONE)
                ->where('idSupermarket = :idSupermarket')
                ->setParameter('idSupermarket', $idSupermarket)
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function search(Id $idSupermarket): array
    {
        $criteria = new Criteria(
            new Filters(
                [
                    new Filter(
                        new FilterField(TableNames::TABLE_ZONE, new FieldName('idSupermarket')),
                        FilterOperator::EQUAL,
                        new FilterValue($idSupermarket)
                    )
                ]
            )
        );
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $zones = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$zones) {
            return [];
        }

        foreach ($zones as $key => $zone) {
            $zones[$key]['spaces'] = $this->spaceDao->search(new Id($zone['id']));
            $zones[$key]['cornerTopLeft'] = Utils::jsonDecode($zone['cornerTopLeft']);
            $zones[$key]['cornerTopRight'] = Utils::jsonDecode($zone['cornerTopRight']);
            $zones[$key]['cornerBottomRight'] = Utils::jsonDecode($zone['cornerBottomRight']);
            $zones[$key]['cornerBottomLeft'] = Utils::jsonDecode($zone['cornerBottomLeft']);
        }

        return $zones;
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE_ZONE,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}