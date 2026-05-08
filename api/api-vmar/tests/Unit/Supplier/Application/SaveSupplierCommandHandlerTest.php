<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Supplier\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Supplier\Application\Save\SaveSupplierCommand;
use SuperVMar\Supplier\Application\Save\SaveSupplierCommandHandler;
use SuperVMar\Supplier\Application\Save\SupplierCreator;
use SuperVMar\Supplier\Application\Save\SupplierUpdater;

final class SaveSupplierCommandHandlerTest extends TestCase
{
    private const string ID      = '550e8400-e29b-41d4-a716-000000000040';
    private const string NAME    = 'Proveedor Test';
    private const string PHONE   = '600000000';
    private const string EMAIL   = 'proveedor@test.com';
    private const string CONTACT = 'Contacto Test';

    private SupplierCreator $creator;
    private SupplierUpdater $updater;
    private SaveSupplierCommandHandler $handler;

    protected function setUp(): void
    {
        $this->creator = $this->createStub(SupplierCreator::class);
        $this->updater = $this->createStub(SupplierUpdater::class);
        $this->handler = new SaveSupplierCommandHandler($this->creator, $this->updater);
    }

    private function command(): SaveSupplierCommand
    {
        return new SaveSupplierCommand(self::ID, self::NAME, self::PHONE, self::EMAIL, self::CONTACT);
    }

    public function test_updates_existing_supplier_when_found(): void
    {
        $updater = $this->createMock(SupplierUpdater::class);
        $updater->expects($this->once())->method('update');
        $creator = $this->createMock(SupplierCreator::class);
        $creator->expects($this->never())->method('create');

        (new SaveSupplierCommandHandler($creator, $updater))($this->command());
    }

    public function test_creates_supplier_when_updater_reports_not_found(): void
    {
        $updater = $this->createMock(SupplierUpdater::class);
        $updater->expects($this->once())->method('update')->willThrowException(
            new ItemNotFoundException('Supplier', ['id' => self::ID])
        );
        $creator = $this->createMock(SupplierCreator::class);
        $creator->expects($this->once())->method('create');

        (new SaveSupplierCommandHandler($creator, $updater))($this->command());
    }

    public function test_does_not_call_creator_when_update_succeeds(): void
    {
        $updater = $this->createMock(SupplierUpdater::class);
        $updater->expects($this->once())->method('update');
        $creator = $this->createMock(SupplierCreator::class);
        $creator->expects($this->never())->method('create');

        (new SaveSupplierCommandHandler($creator, $updater))($this->command());
    }
}
