<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Integration\User\Infrastructure;

use SuperVMar\App\Tests\Fixtures\IntegrationFixtures;
use SuperVMar\App\Tests\Integration\Shared\Infrastructure\DbalTestCase;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\User\Domain\Service\UserSearcher;
use SuperVMar\User\Domain\User;
use SuperVMar\User\Domain\UserRepository;
use SuperVMar\User\Infrastructure\DbalUserRepository;

final class DbalUserRepositoryTest extends DbalTestCase
{
    private UserRepository $repository;
    private UserSearcher $searcher;
    private IntegrationFixtures $fixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(DbalUserRepository::class);
        $this->searcher   = self::getContainer()->get(UserSearcher::class);
        $this->fixtures   = new IntegrationFixtures($this->connection);
        $this->fixtures->loadUser();
    }


    public function test_insert_persists_user_row(): void
    {
        $this->repository->insert($this->buildNewUser());

        $row = $this->findById('user', 'b0000000-0000-0000-0000-000000000001');
        $this->assertNotFalse($row);
        $this->assertSame('new_user', $row['username']);
    }

    public function test_insert_persists_user_data_row(): void
    {
        $this->repository->insert($this->buildNewUser());

        $row = $this->findById('user_data', 'b0000000-0000-0000-0000-000000000002');
        $this->assertNotFalse($row);
        $this->assertSame('New', $row['name']);
        $this->assertSame('User', $row['surname']);
    }

    public function test_insert_throws_on_duplicate_id(): void
    {
        $this->repository->insert($this->buildNewUser());

        $sameIdDifferentData = $this->buildUserWithUsername(
            id:       'b0000000-0000-0000-0000-000000000001',
            dataId:   'b0000000-0000-0000-0000-000000000020',
            addrId:   'b0000000-0000-0000-0000-000000000021',
            username: 'new_user_2',
        );

        $this->expectException(DuplicateItemException::class);
        $this->repository->insert($sameIdDifferentData);
    }


    public function test_update_changes_username(): void
    {
        $user = $this->buildUserFromFixture(username: 'updated_username');

        $this->repository->update($user);

        $row = $this->findById('user', IntegrationFixtures::USER_ID);
        $this->assertSame('updated_username', $row['username']);
    }

    public function test_update_changes_user_data_name(): void
    {
        $user = $this->buildUserFromFixture(name: 'Updated');

        $this->repository->update($user);

        $row = $this->findById('user_data', IntegrationFixtures::USER_DATA_ID);
        $this->assertSame('Updated', $row['name']);
    }


    public function test_delete_removes_user_row(): void
    {
        $this->connection->createQueryBuilder()
            ->delete('worker_allocation')
            ->where('idUser = :id')
            ->setParameter('id', IntegrationFixtures::USER_ID)
            ->executeStatement();

        $this->connection->createQueryBuilder()
            ->delete('supermarket')
            ->where('id = :id')
            ->setParameter('id', IntegrationFixtures::SUPERMARKET_ID)
            ->executeStatement();

        $user = $this->searcher->search(new Id(IntegrationFixtures::USER_ID));
        $this->repository->delete($user);

        $this->assertFalse($this->findById('user', IntegrationFixtures::USER_ID));
    }

    public function test_delete_removes_user_data_row(): void
    {
        $this->connection->createQueryBuilder()
            ->delete('worker_allocation')
            ->where('idUser = :id')
            ->setParameter('id', IntegrationFixtures::USER_ID)
            ->executeStatement();

        $this->connection->createQueryBuilder()
            ->delete('supermarket')
            ->where('id = :id')
            ->setParameter('id', IntegrationFixtures::SUPERMARKET_ID)
            ->executeStatement();

        $user = $this->searcher->search(new Id(IntegrationFixtures::USER_ID));
        $this->repository->delete($user);

        $this->assertFalse($this->findById('user_data', IntegrationFixtures::USER_DATA_ID));
    }


    public function test_search_by_id_returns_correct_user(): void
    {
        $user = $this->searcher->search(new Id(IntegrationFixtures::USER_ID));

        $this->assertSame(IntegrationFixtures::USER_ID, $user->id()->value());
        $this->assertSame('integration_user', $user->username()->value());
    }

    public function test_search_all_returns_at_least_fixture_user(): void
    {
        $users = $this->searcher->searchAll();

        $ids = array_map(fn ($u) => $u->id()->value(), $users->items());
        $this->assertContains(IntegrationFixtures::USER_ID, $ids);
    }

    public function test_search_by_id_throws_when_not_found(): void
    {
        $this->expectException(ItemNotFoundException::class);

        $this->searcher->search(new Id('c0000000-0000-0000-0000-000000000099'));
    }


    public function test_search_job_name_returns_name_when_allocation_exists(): void
    {
        $jobName = $this->repository->searchJobNameByUserId(new Id(IntegrationFixtures::USER_ID));

        $this->assertSame('cajero integración', $jobName);
    }

    public function test_search_job_name_returns_null_when_no_allocation(): void
    {
        $this->repository->insert($this->buildNewUser());

        $jobName = $this->repository->searchJobNameByUserId(
            new Id('b0000000-0000-0000-0000-000000000001')
        );

        $this->assertNull($jobName);
    }


    private function buildUserFromFixture(
        string $username = 'integration_user',
        string $name     = 'Integration',
    ): User {
        return User::fromArray([
            'id'           => IntegrationFixtures::USER_ID,
            'username'     => $username,
            'idUserData'   => IntegrationFixtures::USER_DATA_ID,
            'name'         => $name,
            'surname'      => 'Tester',
            'email'        => 'integration@test.com',
            'phone'        => '699000001',
            'idAddress'    => IntegrationFixtures::ADDR_ID,
            'nameAddress'  => 'Calle Integracion',
            'postalCode'   => '28080',
            'city'         => 'Madrid',
            'number'       => '10',
            'province'     => 'Madrid',
            'floor'        => null,
            'door'         => null,
            'other'        => null,
            'isAdmin'      => 0,
            'password'     => IntegrationFixtures::PASSWORD,
            'allocations'  => [],
        ]);
    }

    private function buildNewUser(): User
    {
        return $this->buildUserWithUsername(
            id:       'b0000000-0000-0000-0000-000000000001',
            dataId:   'b0000000-0000-0000-0000-000000000002',
            addrId:   'b0000000-0000-0000-0000-000000000003',
            username: 'new_user',
        );
    }

    private function buildUserWithUsername(
        string $id,
        string $dataId,
        string $addrId,
        string $username,
    ): User {
        return User::fromArray([
            'id'           => $id,
            'username'     => $username,
            'idUserData'   => $dataId,
            'name'         => 'New',
            'surname'      => 'User',
            'email'        => $username . '@test.com',
            'phone'        => '699000099',
            'idAddress'    => $addrId,
            'nameAddress'  => 'Calle Nueva',
            'postalCode'   => '28001',
            'city'         => 'Madrid',
            'number'       => '1',
            'province'     => 'Madrid',
            'floor'        => null,
            'door'         => null,
            'other'        => null,
            'isAdmin'      => 0,
            'password'     => IntegrationFixtures::PASSWORD,
            'allocations'  => [],
        ]);
    }
}
