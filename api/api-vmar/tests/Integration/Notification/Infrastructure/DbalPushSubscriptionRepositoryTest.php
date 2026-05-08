<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Integration\Notification\Infrastructure;

use SuperVMar\App\Tests\Fixtures\IntegrationFixtures;
use SuperVMar\App\Tests\Integration\Shared\Infrastructure\DbalTestCase;
use SuperVMar\Notification\Domain\PushSubscription;
use SuperVMar\Notification\Domain\PushSubscriptionRepository;
use SuperVMar\Notification\Domain\ValueObject\AuthKey;
use SuperVMar\Notification\Domain\ValueObject\Endpoint;
use SuperVMar\Notification\Domain\ValueObject\P256dhKey;
use SuperVMar\Notification\Infrastructure\DbalPushSubscriptionRepository;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final class DbalPushSubscriptionRepositoryTest extends DbalTestCase
{
    private const string SUB_ID    = 'c0000000-0000-0000-0000-000000000001';
    private const string ENDPOINT  = 'https://fcm.googleapis.com/fcm/send/test-endpoint';
    private const string AUTH_KEY  = 'test-auth-key-base64';
    private const string P256DH    = 'test-p256dh-key-base64';

    private PushSubscriptionRepository $repository;
    private IntegrationFixtures $fixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(DbalPushSubscriptionRepository::class);
        $this->fixtures   = new IntegrationFixtures($this->connection);
        $this->fixtures->loadUser();
    }

    private function buildSubscription(
        string $id = self::SUB_ID,
        string $idUser = IntegrationFixtures::USER_ID,
        string $endpoint = self::ENDPOINT,
        string $authKey = self::AUTH_KEY,
        string $p256dh = self::P256DH,
    ): PushSubscription {
        return PushSubscription::create(
            new Id($id),
            new Id($idUser),
            new Endpoint($endpoint),
            new AuthKey($authKey),
            new P256dhKey($p256dh),
        );
    }


    public function test_insert_persists_push_subscription(): void
    {
        $this->repository->insert($this->buildSubscription());

        $row = $this->findById('push_subscription', self::SUB_ID);
        $this->assertNotFalse($row);
        $this->assertSame(IntegrationFixtures::USER_ID, $row['idUser']);
        $this->assertSame(self::ENDPOINT, $row['endpoint']);
        $this->assertSame(self::AUTH_KEY, $row['authKey']);
        $this->assertSame(self::P256DH, $row['p256dhKey']);
    }

    public function test_insert_throws_on_duplicate_id(): void
    {
        $sub = $this->buildSubscription();
        $this->repository->insert($sub);

        $this->expectException(DuplicateItemException::class);
        $this->repository->insert($sub);
    }


    public function test_update_changes_subscription_data(): void
    {
        $this->repository->insert($this->buildSubscription());

        $updated = $this->buildSubscription(
            endpoint: 'https://fcm.googleapis.com/fcm/send/updated-endpoint',
            authKey:  'new-auth-key',
            p256dh:   'new-p256dh-key',
        );
        $this->repository->update($updated);

        $row = $this->findById('push_subscription', self::SUB_ID);
        $this->assertSame('https://fcm.googleapis.com/fcm/send/updated-endpoint', $row['endpoint']);
        $this->assertSame('new-auth-key', $row['authKey']);
        $this->assertSame('new-p256dh-key', $row['p256dhKey']);
    }


    public function test_delete_by_user_id_removes_row(): void
    {
        $this->repository->insert($this->buildSubscription());

        $this->repository->deleteByUserId(new Id(IntegrationFixtures::USER_ID));

        $row = $this->findById('push_subscription', self::SUB_ID);
        $this->assertFalse($row);
    }


    public function test_search_all_returns_all_subscriptions(): void
    {
        $this->repository->insert($this->buildSubscription());

        $result = $this->repository->searchAll();

        $this->assertGreaterThanOrEqual(1, $result->count());
    }


    public function test_search_by_user_ids_returns_matching_subscriptions(): void
    {
        $this->repository->insert($this->buildSubscription());

        $result = $this->repository->searchByUserIds([new Id(IntegrationFixtures::USER_ID)]);

        $this->assertSame(1, $result->count());
    }

    public function test_search_by_user_ids_returns_empty_for_unknown_user(): void
    {
        $result = $this->repository->searchByUserIds([new Id('c0000000-0000-0000-0000-999999999999')]);

        $this->assertSame(0, $result->count());
    }
}
