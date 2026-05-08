<?php

namespace SuperVMar\Authentication\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\Authentication\Domain\AuthRepository;
use SuperVMar\Authentication\Domain\AuthUser;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use Throwable;

final readonly class DbalAuthRepository implements AuthRepository
{
    private const string TABLE_USER = TableNames::TABLE_USER->value;

    public function __construct(
        private Connection $connection,
        private DbalCriteriaConverter $dbalCriteriaConverter,
    )
    {
    }

    /**
     * @throws InternalErrorException
     * @throws ItemNotFoundException
     */
    public function search(Criteria $criteria): AuthUser
    {
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $user = $query->executeQuery()->fetchAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$user) {
            throw new ItemNotFoundException(AuthUser::class, $criteria->filters()->toArray());
        }

        return AuthUser::fromArray($user);
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE_USER,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}