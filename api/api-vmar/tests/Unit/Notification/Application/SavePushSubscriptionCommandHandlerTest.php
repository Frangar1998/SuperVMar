<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Notification\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Notification\Application\Save\PushSubscriptionCreator;
use SuperVMar\Notification\Application\Save\PushSubscriptionUpdater;
use SuperVMar\Notification\Application\Save\SavePushSubscriptionCommand;
use SuperVMar\Notification\Application\Save\SavePushSubscriptionCommandHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;

final class SavePushSubscriptionCommandHandlerTest extends TestCase
{
    private const string ID        = '550e8400-e29b-41d4-a716-000000000060';
    private const string USER_ID   = '550e8400-e29b-41d4-a716-000000000061';
    private const string ENDPOINT  = 'https://fcm.googleapis.com/fcm/send/test-endpoint';
    private const string AUTH_KEY  = 'test-auth-key-base64';
    private const string P256DH    = 'test-p256dh-key-base64';

    private PushSubscriptionCreator $creator;
    private PushSubscriptionUpdater $updater;
    private SavePushSubscriptionCommandHandler $handler;

    protected function setUp(): void
    {
        $this->creator = $this->createStub(PushSubscriptionCreator::class);
        $this->updater = $this->createStub(PushSubscriptionUpdater::class);
        $this->handler = new SavePushSubscriptionCommandHandler($this->creator, $this->updater);
    }

    private function command(): SavePushSubscriptionCommand
    {
        return new SavePushSubscriptionCommand(
            self::ID,
            self::USER_ID,
            self::ENDPOINT,
            self::AUTH_KEY,
            self::P256DH,
        );
    }

    public function test_updates_existing_subscription_when_found(): void
    {
        $updater = $this->createMock(PushSubscriptionUpdater::class);
        $updater->expects($this->once())->method('update');
        $creator = $this->createMock(PushSubscriptionCreator::class);
        $creator->expects($this->never())->method('create');

        (new SavePushSubscriptionCommandHandler($creator, $updater))($this->command());
    }

    public function test_creates_subscription_when_updater_reports_not_found(): void
    {
        $updater = $this->createMock(PushSubscriptionUpdater::class);
        $updater->expects($this->once())->method('update')->willThrowException(
            new ItemNotFoundException('PushSubscription', ['idUser' => self::USER_ID])
        );
        $creator = $this->createMock(PushSubscriptionCreator::class);
        $creator->expects($this->once())->method('create');

        (new SavePushSubscriptionCommandHandler($creator, $updater))($this->command());
    }

    public function test_does_not_call_creator_when_update_succeeds(): void
    {
        $updater = $this->createMock(PushSubscriptionUpdater::class);
        $updater->expects($this->once())->method('update');
        $creator = $this->createMock(PushSubscriptionCreator::class);
        $creator->expects($this->never())->method('create');

        (new SavePushSubscriptionCommandHandler($creator, $updater))($this->command());
    }
}
