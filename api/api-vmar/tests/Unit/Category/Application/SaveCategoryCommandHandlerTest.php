<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Category\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Category\Application\Save\CategoryCreator;
use SuperVMar\Category\Application\Save\CategoryUpdater;
use SuperVMar\Category\Application\Save\SaveCategoryCommand;
use SuperVMar\Category\Application\Save\SaveCategoryCommandHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;

final class SaveCategoryCommandHandlerTest extends TestCase
{
    private const string ID   = '550e8400-e29b-41d4-a716-000000000030';
    private const string NAME = 'Lácteos';

    private CategoryCreator $creator;
    private CategoryUpdater $updater;
    private SaveCategoryCommandHandler $handler;

    protected function setUp(): void
    {
        $this->creator = $this->createStub(CategoryCreator::class);
        $this->updater = $this->createStub(CategoryUpdater::class);
        $this->handler = new SaveCategoryCommandHandler($this->creator, $this->updater);
    }

    private function command(): SaveCategoryCommand
    {
        return new SaveCategoryCommand(self::ID, self::NAME);
    }

    public function test_updates_existing_category_when_found(): void
    {
        $updater = $this->createMock(CategoryUpdater::class);
        $updater->expects($this->once())->method('update');
        $creator = $this->createMock(CategoryCreator::class);
        $creator->expects($this->never())->method('create');

        (new SaveCategoryCommandHandler($creator, $updater))($this->command());
    }

    public function test_creates_category_when_updater_reports_not_found(): void
    {
        $updater = $this->createMock(CategoryUpdater::class);
        $updater->expects($this->once())->method('update')->willThrowException(
            new ItemNotFoundException('Category', ['id' => self::ID])
        );
        $creator = $this->createMock(CategoryCreator::class);
        $creator->expects($this->once())->method('create');

        (new SaveCategoryCommandHandler($creator, $updater))($this->command());
    }

    public function test_does_not_call_creator_when_update_succeeds(): void
    {
        $updater = $this->createMock(CategoryUpdater::class);
        $updater->expects($this->once())->method('update');
        $creator = $this->createMock(CategoryCreator::class);
        $creator->expects($this->never())->method('create');

        (new SaveCategoryCommandHandler($creator, $updater))($this->command());
    }
}
