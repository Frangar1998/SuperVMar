<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Job\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Job\Application\Save\JobCreator;
use SuperVMar\Job\Application\Save\JobUpdater;
use SuperVMar\Job\Application\Save\SaveJobCommand;
use SuperVMar\Job\Application\Save\SaveJobCommandHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;

final class SaveJobCommandHandlerTest extends TestCase
{
    private const string ID   = '550e8400-e29b-41d4-a716-000000000041';
    private const string NAME = 'Cajero';

    private JobCreator $creator;
    private JobUpdater $updater;
    private SaveJobCommandHandler $handler;

    protected function setUp(): void
    {
        $this->creator = $this->createStub(JobCreator::class);
        $this->updater = $this->createStub(JobUpdater::class);
        $this->handler = new SaveJobCommandHandler($this->creator, $this->updater);
    }

    private function command(): SaveJobCommand
    {
        return new SaveJobCommand(self::ID, self::NAME);
    }

    public function test_updates_existing_job_when_found(): void
    {
        $updater = $this->createMock(JobUpdater::class);
        $updater->expects($this->once())->method('update');
        $creator = $this->createMock(JobCreator::class);
        $creator->expects($this->never())->method('create');

        (new SaveJobCommandHandler($creator, $updater))($this->command());
    }

    public function test_creates_job_when_updater_reports_not_found(): void
    {
        $updater = $this->createMock(JobUpdater::class);
        $updater->expects($this->once())->method('update')->willThrowException(
            new ItemNotFoundException('Job', ['id' => self::ID])
        );
        $creator = $this->createMock(JobCreator::class);
        $creator->expects($this->once())->method('create');

        (new SaveJobCommandHandler($creator, $updater))($this->command());
    }

    public function test_does_not_call_creator_when_update_succeeds(): void
    {
        $updater = $this->createMock(JobUpdater::class);
        $updater->expects($this->once())->method('update');
        $creator = $this->createMock(JobCreator::class);
        $creator->expects($this->never())->method('create');

        (new SaveJobCommandHandler($creator, $updater))($this->command());
    }
}
