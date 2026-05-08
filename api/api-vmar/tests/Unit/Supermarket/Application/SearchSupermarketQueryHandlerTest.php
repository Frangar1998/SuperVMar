<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Supermarket\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;
use SuperVMar\Supermarket\Application\Search\Supermarket\SearchSupermarketQuery;
use SuperVMar\Supermarket\Application\Search\Supermarket\SearchSupermarketQueryHandler;
use SuperVMar\Supermarket\Application\Search\Supermarket\SupermarketResponse;
use SuperVMar\Supermarket\Domain\Service\SupermarketSearcher;
use SuperVMar\Supermarket\Domain\Supermarket;
use SuperVMar\Supermarket\Domain\Entity\Address;
use SuperVMar\Supermarket\Domain\Entity\Zones;
use SuperVMar\Supermarket\Domain\ValueObject\Email;
use SuperVMar\Supermarket\Domain\ValueObject\Phone;

final class SearchSupermarketQueryHandlerTest extends TestCase
{
    private const string SM_ID   = '550e8400-e29b-41d4-a716-000000000050';
    private const string ADDR_ID = '550e8400-e29b-41d4-a716-000000000051';

    private static function buildSupermarket(): Supermarket
    {
        return Supermarket::create(
            new Id(self::SM_ID),
            new Name('Test SM'),
            Address::fromArray([
                'id'         => self::ADDR_ID,
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

    public function test_returns_supermarket_response_with_correct_fields(): void
    {
        $searcher = $this->createStub(SupermarketSearcher::class);
        $searcher->method('search')->willReturn(self::buildSupermarket());

        $handler = new SearchSupermarketQueryHandler($searcher);
        $response = $handler(new SearchSupermarketQuery(self::SM_ID));

        $this->assertInstanceOf(SupermarketResponse::class, $response);
        $data = $response->toArray();
        $this->assertSame(self::SM_ID, $data['id']);
        $this->assertSame('Test SM', $data['name']);
        $this->assertSame('600000000', $data['phone']);
        $this->assertSame('test@test.com', $data['email']);
        $this->assertIsArray($data['address']);
        $this->assertSame(self::ADDR_ID, $data['address']['id']);
        $this->assertIsArray($data['zones']);
    }
}
