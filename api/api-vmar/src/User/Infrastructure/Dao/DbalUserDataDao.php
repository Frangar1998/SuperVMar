<?php

namespace SuperVMar\User\Infrastructure\Dao;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\User\Domain\Entity\UserData;
use Throwable;

final readonly class DbalUserDataDao
{
    private const string TABLE_USER_DATA = TableNames::TABLE_USER_DATA->value;

    public function __construct(
        private Connection $connection,
        private DbalAddressDao $addressDao
    )
    {
    }

    /**
     * @throws InternalErrorException
     * @throws DuplicateItemException
     */
    public function insert(UserData $userData): void
    {
        try {
            $this->addressDao->insert($userData->address());

            $this->connection->createQueryBuilder()
                ->insert(self::TABLE_USER_DATA)
                ->values(
                    [
                        'id' => ':id',
                        'name' => ':name',
                        'surname' => ':surname',
                        'email' => ':email',
                        'phone' => ':phone',
                        'idAddress' => ':idAddress',
                    ])
                ->setParameters(
                    [
                        'id' => $userData->id(),
                        'name' => $userData->name(),
                        'surname' => $userData->surname(),
                        'email' => $userData->email(),
                        'phone' => $userData->phone(),
                        'idAddress' => $userData->address()->id()
                    ])
                ->executeStatement();

        } catch (UniqueConstraintViolationException) {
            throw new DuplicateItemException(UserData::class, $userData->id());
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(UserData $userData): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE_USER_DATA)
                ->set('name', ':name')
                ->set('surname', ':surname')
                ->set('email', ':email')
                ->set('phone', ':phone')
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $userData->id(),
                        'name' => $userData->name(),
                        'surname' => $userData->surname(),
                        'email' => $userData->email(),
                        'phone' => $userData->phone(),
                    ])
                ->executeStatement();

            $this->addressDao->update($userData->address());

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function delete(UserData $userData): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_USER_DATA)
                ->where('id = :id')
                ->setParameter('id', $userData->id())
                ->executeStatement();

            $this->addressDao->delete($userData->address()->id());
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }
}