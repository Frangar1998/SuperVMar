<?php

namespace SuperVMar\Notification\Infrastructure;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\Notification\Domain\PushSubscription;
use SuperVMar\Notification\Domain\PushSubscriptionRepository;
use SuperVMar\Notification\Domain\PushSubscriptions;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use Throwable;

final readonly class DbalPushSubscriptionRepository implements PushSubscriptionRepository
{
    private const string TABLE = TableNames::TABLE_PUSH_SUBSCRIPTION->value;

    public function __construct(
        private Connection $connection,
        private DbalCriteriaConverter $dbalCriteriaConverter,
    )
    {
    }

    /**
     * @throws DuplicateItemException
     * @throws InternalErrorException
     */
    public function insert(PushSubscription $pushSubscription): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->insert(self::TABLE)
                ->values(
                    [
                        'id' => ':id',
                        'idUser' => ':idUser',
                        'endpoint' => ':endpoint',
                        'authKey' => ':authKey',
                        'p256dhKey' => ':p256dhKey',
                    ])
                ->setParameters(
                    [
                        'id' => $pushSubscription->id()->value(),
                        'idUser' => $pushSubscription->idUser()->value(),
                        'endpoint' => $pushSubscription->endpoint()->value(),
                        'authKey' => $pushSubscription->authKey()->value(),
                        'p256dhKey' => $pushSubscription->p256dhKey()->value(),
                    ])
                ->executeStatement();

        } catch (UniqueConstraintViolationException) {
            throw new DuplicateItemException(PushSubscription::class, $pushSubscription->id()->value());
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(PushSubscription $pushSubscription): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE)
                ->set('endpoint', ':endpoint')
                ->set('authKey', ':authKey')
                ->set('p256dhKey', ':p256dhKey')
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $pushSubscription->id()->value(),
                        'endpoint' => $pushSubscription->endpoint()->value(),
                        'authKey' => $pushSubscription->authKey()->value(),
                        'p256dhKey' => $pushSubscription->p256dhKey()->value(),
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function deleteByUserId(Id $idUser): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE)
                ->where('idUser = :idUser')
                ->setParameter('idUser', $idUser->value())
                ->executeStatement();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    public function searchByUserIds(array $userIds): PushSubscriptions
    {
        try {
            $ids = array_map(fn(Id $id) => $id->value(), $userIds);

            $result = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->where('idUser IN (:userIds)')
                ->setParameter('userIds', $ids, ArrayParameterType::STRING)
                ->executeQuery()
                ->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        return PushSubscriptions::fromArray($result);
    }

    /**
     * @throws InternalErrorException
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): PushSubscriptions
    {
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $subscriptions = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$subscriptions) {
            throw new ItemNotFoundException(PushSubscription::class, $criteria->filters()?->toArray() ?? []);
        }

        return PushSubscriptions::fromArray($subscriptions);
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}
