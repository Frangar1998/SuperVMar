<?php

namespace SuperVMar\User\Infrastructure;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use SuperVMar\User\Domain\User;
use SuperVMar\User\Domain\UserRepository;
use SuperVMar\User\Domain\Users;
use SuperVMar\User\Infrastructure\Dao\DbalUserDataDao;
use SuperVMar\User\Infrastructure\Dao\DbalAllocationDao;
use Throwable;

final readonly class DbalUserRepository implements UserRepository
{
    private const string TABLE_USER = TableNames::TABLE_USER->value;

    public function __construct(
        private Connection $connection,
        private DbalCriteriaConverter $dbalCriteriaConverter,
        private DbalUserDataDao $userDataDao,
        private DbalAllocationDao $allocationDataDao,
    )
    {
    }

    /**
     * @throws DuplicateItemException
     * @throws InternalErrorException
     */
    public function insert(User $user): void
    {
        try {
            $this->userDataDao->insert($user->userData());

            $this->connection->createQueryBuilder()
                ->insert(self::TABLE_USER)
                ->values(
                    [
                        'id' => ':id',
                        'username' => ':username',
                        'password' => ':password',
                        'isAdmin' => ':isAdmin',
                        'idUserData' => ':idUserData'
                    ])
                ->setParameters(
                    [
                        'id' => $user->id(),
                        'username' => $user->username(),
                        'password' => $user->password(),
                        'isAdmin' => $user->isAdmin(),
                        'idUserData' => $user->userData()->id()
                    ])
                ->executeStatement();

        } catch (UniqueConstraintViolationException) {
            throw new DuplicateItemException(User::class, $user->id());
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(User $user): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE_USER)
                ->set('username', ':username')
                ->set('password', ':password')
                ->set('isAdmin', ':isAdmin')
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $user->id(),
                        'username' => $user->username(),
                        'password' => $user->password(),
                        'isAdmin' => $user->isAdmin()
                    ])
                ->executeStatement();

            $this->userDataDao->update($user->userData());

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function delete(User $user): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_USER)
                ->where('id = :id')
                ->setParameter('id', $user->id())
                ->executeStatement();

            $this->userDataDao->delete($user->userData());
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): Users
    {
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $users = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$users) {
            throw new ItemNotFoundException(User::class, $criteria->filters()->toArray());
        }

        foreach ($users as $key => $user) {
            $users[$key]['allocations'] = $this->allocationDataDao->search(new Id($user['id']));
        }

        return Users::fromArray($users);
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