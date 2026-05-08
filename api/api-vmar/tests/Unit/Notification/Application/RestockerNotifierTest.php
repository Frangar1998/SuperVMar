<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Notification\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Notification\Application\Send\RestockerNotifier;
use SuperVMar\Notification\Domain\PushSubscriptionRepository;
use SuperVMar\Notification\Domain\PushSubscriptions;
use SuperVMar\Notification\Domain\WebPushSender;

final class RestockerNotifierTest extends TestCase
{
    private const string ZONE_ID      = '550e8400-e29b-41d4-a716-000000000052';
    private const string PRODUCT_NAME = 'Leche Entera';
    private const string ZONE_NAME    = 'Zona A';

    private PushSubscriptionRepository $repository;
    private WebPushSender $webPushSender;
    private RestockerNotifier $notifier;

    protected function setUp(): void
    {
        $this->repository    = $this->createMock(PushSubscriptionRepository::class);
        $this->webPushSender = $this->createMock(WebPushSender::class);
        $this->notifier      = new RestockerNotifier($this->repository, $this->webPushSender);
    }

    private function buildSubscriptions(): PushSubscriptions
    {
        return PushSubscriptions::fromArray([
            [
                'id'        => '550e8400-e29b-41d4-a716-000000000060',
                'idUser'    => '550e8400-e29b-41d4-a716-000000000061',
                'endpoint'  => 'https://fcm.googleapis.com/fcm/send/test',
                'authKey'   => 'test-auth-key',
                'p256dhKey' => 'test-p256dh-key',
            ],
        ]);
    }

    public function test_does_not_send_when_no_subscriptions(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('searchAll')
            ->willReturn(new PushSubscriptions([]));

        $this->webPushSender->expects($this->never())->method('send');

        $this->notifier->notify(self::ZONE_ID, self::PRODUCT_NAME, self::ZONE_NAME, 2);
    }

    public function test_sends_when_subscriptions_exist(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('searchAll')
            ->willReturn($this->buildSubscriptions());

        $this->webPushSender->expects($this->once())->method('send');

        $this->notifier->notify(self::ZONE_ID, self::PRODUCT_NAME, self::ZONE_NAME, 2);
    }

    public function test_payload_uses_empty_status_when_quantity_is_zero(): void
    {
        $capturedPayload = null;
        $this->repository
            ->expects($this->once())
            ->method('searchAll')
            ->willReturn($this->buildSubscriptions());

        $this->webPushSender
            ->expects($this->once())
            ->method('send')
            ->willReturnCallback(function ($subs, $payload) use (&$capturedPayload) {
                $capturedPayload = $payload;
            });

        $this->notifier->notify(self::ZONE_ID, self::PRODUCT_NAME, self::ZONE_NAME, 0);

        $this->assertStringContainsString('VACÍO', $capturedPayload['body']);
        $this->assertSame('empty', $capturedPayload['data']['status']);
    }

    public function test_payload_uses_low_status_when_quantity_above_zero(): void
    {
        $capturedPayload = null;
        $this->repository
            ->expects($this->once())
            ->method('searchAll')
            ->willReturn($this->buildSubscriptions());

        $this->webPushSender
            ->expects($this->once())
            ->method('send')
            ->willReturnCallback(function ($subs, $payload) use (&$capturedPayload) {
                $capturedPayload = $payload;
            });

        $this->notifier->notify(self::ZONE_ID, self::PRODUCT_NAME, self::ZONE_NAME, 1);

        $this->assertStringContainsString('STOCK BAJO', $capturedPayload['body']);
        $this->assertSame('low', $capturedPayload['data']['status']);
    }
}
