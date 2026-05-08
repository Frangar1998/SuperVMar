<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Supermarket\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;
use SuperVMar\Supermarket\Application\Search\Supermarket\SearchSupermarketsQuery;
use SuperVMar\Supermarket\Application\Search\Supermarket\SearchSupermarketsQueryHandler;
use SuperVMar\Supermarket\Application\Search\Supermarket\SupermarketsResponse;
use SuperVMar\Supermarket\Domain\Service\SupermarketSearcher;
use SuperVMar\Supermarket\Domain\Supermarket;
use SuperVMar\Supermarket\Domain\Supermarkets;
use SuperVMar\Supermarket\Domain\Entity\Address;
use SuperVMar\Supermarket\Domain\Entity\Zones;
use SuperVMar\Supermarket\Domain\ValueObject\Email;
use SuperVMar\Supermarket\Domain\ValueObject\Phone;

final class SearchSupermarketsQueryHandlerTest extends TestCase
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

    public function test_returns_supermarkets_response(): void
    {
        $searcher = $this->createStub(SupermarketSearcher::class);
        $searcher->method('searchAll')->willReturn(new Supermarkets([self::buildSupermarket()]));

        $handler = new SearchSupermarketsQueryHandler($searcher);
        $response = $handler(new SearchSupermarketsQuery());

        $this->assertInstanceOf(SupermarketsResponse::class, $response);
        $this->assertCount(1, $response->toArray());
    }

    public function test_returns_empty_response_when_no_supermarkets(): void
    {
        $searcher = $this->createStub(SupermarketSearcher::class);
        $searcher->method('searchAll')->willReturn(new Supermarkets([]));

        $handler = new SearchSupermarketsQueryHandler($searcher);
        $response = $handler(new SearchSupermarketsQuery());

        $this->assertInstanceOf(SupermarketsResponse::class, $response);
        $this->assertSame([], $response->toArray());
    }
}
