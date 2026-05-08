<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Tax\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Tax\Application\Save\SaveTaxCommand;
use SuperVMar\Tax\Application\Save\SaveTaxCommandHandler;
use SuperVMar\Tax\Application\Save\TaxCreator;
use SuperVMar\Tax\Application\Save\TaxUpdater;

final class SaveTaxCommandHandlerTest extends TestCase
{
    private const string ID   = '550e8400-e29b-41d4-a716-000000000020';
    private const string NAME = 'IVA 21%';
    private const float PCT   = 21.0;

    private TaxCreator $creator;
    private TaxUpdater $updater;
    private SaveTaxCommandHandler $handler;

    protected function setUp(): void
    {
        $this->creator = $this->createStub(TaxCreator::class);
        $this->updater = $this->createStub(TaxUpdater::class);
        $this->handler = new SaveTaxCommandHandler($this->creator, $this->updater);
    }

    private function command(): SaveTaxCommand
    {
        return new SaveTaxCommand(self::ID, self::NAME, self::PCT);
    }

    public function test_updates_existing_tax_when_found(): void
    {
        $updater = $this->createMock(TaxUpdater::class);
        $updater->expects($this->once())->method('update');
        $creator = $this->createMock(TaxCreator::class);
        $creator->expects($this->never())->method('create');

        (new SaveTaxCommandHandler($creator, $updater))($this->command());
    }

    public function test_creates_tax_when_updater_reports_not_found(): void
    {
        $updater = $this->createMock(TaxUpdater::class);
        $updater->expects($this->once())->method('update')->willThrowException(
            new ItemNotFoundException('Tax', ['id' => self::ID])
        );
        $creator = $this->createMock(TaxCreator::class);
        $creator->expects($this->once())->method('create');

        (new SaveTaxCommandHandler($creator, $updater))($this->command());
    }

    public function test_does_not_call_creator_when_update_succeeds(): void
    {
        $updater = $this->createMock(TaxUpdater::class);
        $updater->expects($this->once())->method('update');
        $creator = $this->createMock(TaxCreator::class);
        $creator->expects($this->never())->method('create');

        (new SaveTaxCommandHandler($creator, $updater))($this->command());
    }

    public function test_accepts_zero_percent_tax(): void
    {
        $updater = $this->createMock(TaxUpdater::class);
        $updater->expects($this->once())->method('update')->willThrowException(
            new ItemNotFoundException('Tax', ['id' => self::ID])
        );
        $creator = $this->createMock(TaxCreator::class);
        $creator->expects($this->once())->method('create');

        (new SaveTaxCommandHandler($creator, $updater))(
            new SaveTaxCommand(self::ID, 'Exento', 0.0)
        );
    }
}
