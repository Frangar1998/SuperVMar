<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Integration\Shared\Infrastructure;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class DbalTestCase extends KernelTestCase
{
    protected Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->connection = self::getContainer()->get(Connection::class);
        $this->connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->connection->rollBack();
        parent::tearDown();
    }

    /**
     * Helper to fetch a single row from a table by id.
     */
    protected function findById(string $table, string $id): array|false
    {
        return $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from($table)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
    }

    /**
     * Helper to count rows in a table matching optional WHERE conditions.
     *
     * @param array<string, mixed> $where
     */
    protected function countRows(string $table, array $where = []): int
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->select('COUNT(*)')
            ->from($table);

        foreach ($where as $field => $value) {
            $qb->andWhere("$field = :$field")
               ->setParameter($field, $value);
        }

        return (int) $qb->executeQuery()->fetchOne();
    }
}
