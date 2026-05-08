<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Supermarket\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;
use SuperVMar\Supermarket\Application\Search\Zones\SearchZonesQuery;
use SuperVMar\Supermarket\Application\Search\Zones\SearchZonesQueryHandler;
use SuperVMar\Supermarket\Application\Search\Zones\ZonesResponse;
use SuperVMar\Supermarket\Domain\Service\SupermarketSearcher;
use SuperVMar\Supermarket\Domain\Supermarket;
use SuperVMar\Supermarket\Domain\Entity\Address;
use SuperVMar\Supermarket\Domain\Entity\Zones;
use SuperVMar\Supermarket\Domain\ValueObject\Email;
use SuperVMar\Supermarket\Domain\ValueObject\Phone;

final class SearchZonesQueryHandlerTest extends TestCase
{
    private const string SM_ID = '550e8400-e29b-41d4-a716-000000000050';

    private static function buildSupermarket(): Supermarket
    {
        return Supermarket::create(
            new Id(self::SM_ID),
            new Name('Test SM'),
            Address::fromArray([
                'id'         => '550e8400-e29b-41d4-a716-000000000051',
                'name'       => 'Calle Test',
                'postalCode' => '28080',
                'city'       => 'Madrid',
                'number'     => '1',
                'province'   => 'Madrid',
            ]),
            new Phone('600000000'),
            new Email('test@test.com'),
            Zones::fromArray([])
        );
    }

    public function test_returns_zones_response_with_zones_key(): void
    {
        $searcher = $this->createStub(SupermarketSearcher::class);
        $searcher->method('search')->willReturn(self::buildSupermarket());

        $handler = new SearchZonesQueryHandler($searcher);
        $response = $handler(new SearchZonesQuery(self::SM_ID));

        $this->assertInstanceOf(ZonesResponse::class, $response);
        $data = $response->toArray();
        $this->assertArrayHasKey('zones', $data);
        $this->assertIsArray($data['zones']);
    }

    public function test_zones_empty_when_no_zones(): void
    {
        $searcher = $this->createStub(SupermarketSearcher::class);
        $searcher->method('search')->willReturn(self::buildSupermarket());

        $handler = new SearchZonesQueryHandler($searcher);
        $response = $handler(new SearchZonesQuery(self::SM_ID));

        $this->assertSame([], $response->toArray()['zones']);
    }
}
