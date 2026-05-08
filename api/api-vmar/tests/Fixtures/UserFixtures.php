<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Fixtures;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;

/**
 * Fixtures for testing authentication and user-related endpoints.
 *
 * Creates:
 *   - test_admin  (isAdmin=1, password: Test1234!)
 *   - test_cajero (isAdmin=0, job contains "cajero", password: Test1234!)
 *
 * All IDs are deterministic UUIDs prefixed with 'f' to avoid collisions.
 */
final class UserFixtures extends BaseFixtures
{
    public const string ADMIN_ID            = 'f0000000-0000-0000-0000-000000000001';
    public const string ADMIN_DATA_ID       = 'f0000000-0000-0000-0000-000000000002';
    public const string CAJERO_ID           = 'f0000000-0000-0000-0000-000000000003';
    public const string CAJERO_DATA_ID      = 'f0000000-0000-0000-0000-000000000004';
    public const string ADDRESS_ID          = 'f0000000-0000-0000-0000-000000000005';
    public const string SUPERMARKET_ID      = 'f0000000-0000-0000-0000-000000000010';
    public const string JOB_CAJERO_ID       = 'f0000000-0000-0000-0000-000000000011';

    public const string PASSWORD = 'Test1234!@#$';

    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }

    public function load(ObjectManager $manager): void
    {
        $hashedPassword = $this->hashPassword(self::PASSWORD);

        $this->insert('address', [
            'id'         => self::ADDRESS_ID,
            'name'       => 'Calle Test',
            'postalCode' => '28001',
            'city'       => 'Madrid',
            'number'     => '1',
            'province'   => 'Madrid',
        ]);

        $this->insert('supermarket', [
            'id'        => self::SUPERMARKET_ID,
            'name'      => 'Test Supermarket',
            'email'     => 'supermarket@test.com',
            'idAddress' => self::ADDRESS_ID,
        ]);

        $this->insert('job', [
            'id'   => self::JOB_CAJERO_ID,
            'name' => 'cajero test',
        ]);

        $this->insert('user_data', [
            'id'        => self::ADMIN_DATA_ID,
            'name'      => 'Admin',
            'surname'   => 'Test',
            'email'     => 'admin@test.com',
            'phone'     => '600000001',
            'idAddress' => self::ADDRESS_ID,
        ]);

        $this->insert('user', [
            'id'         => self::ADMIN_ID,
            'username'   => 'test_admin',
            'password'   => $hashedPassword,
            'isAdmin'    => 1,
            'idUserData' => self::ADMIN_DATA_ID,
        ]);

        $this->insert('user_data', [
            'id'        => self::CAJERO_DATA_ID,
            'name'      => 'Cajero',
            'surname'   => 'Test',
            'email'     => 'cajero@test.com',
            'phone'     => '600000002',
            'idAddress' => self::ADDRESS_ID,
        ]);

        $this->insert('user', [
            'id'         => self::CAJERO_ID,
            'username'   => 'test_cajero',
            'password'   => $hashedPassword,
            'isAdmin'    => 0,
            'idUserData' => self::CAJERO_DATA_ID,
        ]);

        $this->insert('worker_allocation', [
            'idUser'        => self::CAJERO_ID,
            'idSupermarket' => self::SUPERMARKET_ID,
            'idJob'         => self::JOB_CAJERO_ID,
        ]);
    }
}
