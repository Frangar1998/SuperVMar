<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Connection;

/**
 * Base class for all fixtures.
 * Provides helpers for direct DBAL inserts, which are used instead of ORM
 * because the project uses DBAL repositories (no ORM entity mappings).
 */
abstract class BaseFixtures extends Fixture
{
    public function __construct(protected readonly Connection $connection) {}

    /**
     * Inserts a row into a table using DBAL.
     *
     * @param array<string, mixed> $data
     */
    protected function insert(string $table, array $data): void
    {
        $this->connection->insert($table, $data);
    }

    /**
     * Generates a bcrypt hash for a plain-text password.
     * Uses cost=4 for speed during tests (not for production use).
     */
    protected function hashPassword(string $plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 4]);
    }
}
