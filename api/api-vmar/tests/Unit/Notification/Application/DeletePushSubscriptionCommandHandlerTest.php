<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Notification\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Notification\Application\Delete\DeletePushSubscriptionCommand;
use SuperVMar\Notification\Application\Delete\DeletePushSubscriptionCommandHandler;
use SuperVMar\Notification\Application\Delete\PushSubscriptionDeleter;

final class DeletePushSubscriptionCommandHandlerTest extends TestCase
{
    private const string USER_ID = '550e8400-e29b-41d4-a716-000000000061';

    private PushSubscriptionDeleter $deleter;
    private DeletePushSubscriptionCommandHandler $handler;

    protected function setUp(): void
    {
        $this->deleter = $this->createMock(PushSubscriptionDeleter::class);
        $this->handler = new DeletePushSubscriptionCommandHandler($this->deleter);
    }

    public function test_delegates_deletion_to_deleter(): void
    {
        $this->deleter->expects($this->once())->method('delete');

        ($this->handler)(new DeletePushSubscriptionCommand(self::USER_ID));
    }

    public function test_calls_deleter_with_correct_user_id(): void
    {
        $capturedId = null;
        $this->deleter
            ->expects($this->once())
            ->method('delete')
            ->willReturnCallback(function ($id) use (&$capturedId) {
                $capturedId = $id;
            });

        ($this->handler)(new DeletePushSubscriptionCommand(self::USER_ID));

        $this->assertSame(self::USER_ID, $capturedId->value());
    }
}
