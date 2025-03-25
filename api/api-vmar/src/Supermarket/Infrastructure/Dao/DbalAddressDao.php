<?php

namespace SuperVMar\Supermarket\Infrastructure\Dao;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Supermarket\Domain\Entity\Address;
use SuperVMar\Supermarket\Domain\ValueObject\Id;
use Throwable;

final readonly class DbalAddressDao
{
    private const string TABLE_ADDRESS = TableNames::TABLE_ADDRESS->value;

    public function __construct(
        private Connection $connection
    )
    {
    }

    /**
     * @throws InternalErrorException
     * @throws DuplicateItemException
     */
    public function insert(Address $address): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->insert(self::TABLE_ADDRESS)
                ->values(
                    [
                        'id' => ':id',
                        'name' => ':name',
                        'postalCode' => ':postalCode',
                        'city' => ':city',
                        'number' => ':number',
                        'province' => ':province',
                    ])
                ->setParameters(
                    [
                        'id' => $address->id(),
                        'name' => $address->name(),
                        'postalCode' => $address->postalCode(),
                        'city' => $address->city(),
                        'number' => $address->number(),
                        'province' => $address->province(),
                    ])
                ->executeStatement();
        } catch (UniqueConstraintViolationException) {
            throw new DuplicateItemException(Address::class, $address->id());
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(Address $address): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE_ADDRESS)
                ->set('name', ':name')
                ->set('postalCode', ':postalCode')
                ->set('city', ':city')
                ->set('number', ':number')
                ->set('province', ':province')
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $address->id(),
                        'name' => $address->name(),
                        'postalCode' => $address->postalCode(),
                        'city' => $address->city(),
                        'number' => $address->number(),
                        'province' => $address->province(),
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function delete(Id $addressId): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_ADDRESS)
                ->where('id = :id')
                ->setParameter('id', $addressId)
                ->executeStatement();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

    }
}