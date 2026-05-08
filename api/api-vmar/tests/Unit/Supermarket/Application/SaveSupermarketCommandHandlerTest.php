<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Supermarket\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;
use SuperVMar\Supermarket\Application\Save\SaveSupermarketCommand;
use SuperVMar\Supermarket\Application\Save\SaveSupermarketCommandHandler;
use SuperVMar\Supermarket\Application\Save\SupermarketCreator;
use SuperVMar\Supermarket\Application\Save\SupermarketUpdater;
use SuperVMar\Supermarket\Domain\Entity\Address;
use SuperVMar\Supermarket\Domain\Entity\Zones;
use SuperVMar\Supermarket\Domain\ValueObject\Email;
use SuperVMar\Supermarket\Domain\ValueObject\Phone;

final class SaveSupermarketCommandHandlerTest extends TestCase
{
    private const string SM_ID = '550e8400-e29b-41d4-a716-000000000050';

    private static function address(): array
    {
        return [
            'id'         => '550e8400-e29b-41d4-a716-000000000051',
            'name'       => 'Calle Test',
            'postalCode' => '28080',
            'city'       => 'Madrid',
            'number'     => '1',
            'province'   => 'Madrid',
        ];
    }

    private function buildCommand(): SaveSupermarketCommand
    {
        return new SaveSupermarketCommand(
            id:      self::SM_ID,
            name:    'Test Supermarket',
            address: self::address(),
            phone:   '600000000',
            email:   'test@test.com',
            zones:   [],
        );
    }

    public function test_calls_updater_when_supermarket_exists(): void
    {
        $creator = $this->createStub(SupermarketCreator::class);
        $updater = $this->createMock(SupermarketUpdater::class);

        $updater->expects($this->once())->method('update');

        $handler = new SaveSupermarketCommandHandler($creator, $updater);
        $handler($this->buildCommand());
    }

    public function test_calls_creator_when_supermarket_not_found(): void
    {
        $updater = $this->createStub(SupermarketUpdater::class);
        $creator = $this->createMock(SupermarketCreator::class);

        $updater->method('update')->willThrowException(new ItemNotFoundException('Supermarket not found', []));
        $creator->expects($this->once())->method('create');

        $handler = new SaveSupermarketCommandHandler($creator, $updater);
        $handler($this->buildCommand());
    }
}
