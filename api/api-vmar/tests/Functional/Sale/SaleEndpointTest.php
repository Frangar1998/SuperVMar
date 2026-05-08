<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Functional\Sale;

use SuperVMar\App\Tests\Fixtures\IntegrationFixtures;
use SuperVMar\App\Tests\Functional\Shared\ApiTestCase;

/**
 * Functional tests for sale endpoints.
 *
 * @covers \SuperVMar\App\Controller\Sale\SaleGetController
 * @covers \SuperVMar\App\Controller\Sale\SalesGetController
 * @covers \SuperVMar\App\Controller\Sale\SaleLinePutController
 * @covers \SuperVMar\App\Controller\Sale\SaleFinishPutController
 * @covers \SuperVMar\App\Controller\Sale\SaleDeleteController
 */
final class SaleEndpointTest extends ApiTestCase
{
    private IntegrationFixtures $fixtures;

    /** A deterministic ID for an open (unfinished) sale used in several tests. */
    private const string OPEN_SALE_ID = 'c0000000-0000-0000-0000-000000000020';

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixtures = new IntegrationFixtures($this->connection);
        $this->connection->executeStatement('DELETE FROM sale_line WHERE idSale IN (?, ?)', [IntegrationFixtures::SALE_ID, self::OPEN_SALE_ID]);
        $this->connection->executeStatement('DELETE FROM sale WHERE id IN (?, ?)', [IntegrationFixtures::SALE_ID, self::OPEN_SALE_ID]);
        $this->connection->executeStatement('DELETE FROM price_history WHERE idProduct = ?', [IntegrationFixtures::PRODUCT_ID]);
        $this->connection->executeStatement('DELETE FROM product WHERE id = ?', [IntegrationFixtures::PRODUCT_ID]);
        $this->connection->executeStatement('DELETE FROM supplier WHERE id = ?', [IntegrationFixtures::SUPPLIER_ID]);
        $this->connection->executeStatement('DELETE FROM category WHERE id = ?', [IntegrationFixtures::CATEGORY_ID]);
        $this->connection->executeStatement('DELETE FROM tax WHERE id = ?', [IntegrationFixtures::TAX_ID]);
        $this->fixtures->loadCatalog();
        $this->fixtures->loadProduct();
        $this->fixtures->loadSale();

        $this->connection->insert('sale', [
            'id'          => self::OPEN_SALE_ID,
            'amount'      => '0.00',
            'taxes'       => '0.00',
            'totalAmount' => '0.00',
            'payMethod'   => 'none',
        ]);
    }

    protected function tearDown(): void
    {
        $this->connection->executeStatement('DELETE FROM sale_line WHERE idSale IN (?, ?, ?)', [
            IntegrationFixtures::SALE_ID,
            self::OPEN_SALE_ID,
            'c0000000-0000-0000-0000-000000000021',
        ]);
        $this->connection->executeStatement("DELETE FROM sale_line WHERE id LIKE 'c00000%'");
        $this->connection->executeStatement('DELETE FROM sale WHERE id IN (?, ?)', [
            IntegrationFixtures::SALE_ID,
            self::OPEN_SALE_ID,
        ]);
        $this->connection->executeStatement("DELETE FROM sale WHERE id = 'c0000000-0000-0000-0000-000000000021'");
        $this->connection->executeStatement('DELETE FROM price_history WHERE idProduct = ?', [IntegrationFixtures::PRODUCT_ID]);
        $this->connection->executeStatement('DELETE FROM product WHERE id = ?', [IntegrationFixtures::PRODUCT_ID]);
        $this->connection->executeStatement('DELETE FROM supplier WHERE id = ?', [IntegrationFixtures::SUPPLIER_ID]);
        $this->connection->executeStatement('DELETE FROM category WHERE id = ?', [IntegrationFixtures::CATEGORY_ID]);
        $this->connection->executeStatement('DELETE FROM tax WHERE id = ?', [IntegrationFixtures::TAX_ID]);
        parent::tearDown();
    }


    public function test_get_sale_as_cajero_returns_200(): void
    {
        $this->jsonRequest('GET', '/api/v1/sale/' . IntegrationFixtures::SALE_ID, [], $this->cajeroToken);

        $this->assertStatusCode(200);
        $response = $this->responseJson();
        $this->assertArrayHasKey('id', $response);
        $this->assertSame(IntegrationFixtures::SALE_ID, $response['id']);
    }

    public function test_get_sale_not_found_returns_404(): void
    {
        $this->jsonRequest(
            'GET',
            '/api/v1/sale/00000000-0000-0000-0000-000000000000',
            [],
            $this->cajeroToken
        );

        $this->assertStatusCode(404);
    }


    public function test_get_sales_returns_200_with_list(): void
    {
        $this->client->request(
            'GET',
            '/api/v1/sales',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->cajeroToken]
        );

        $this->assertStatusCode(200);
        $response = $this->responseJson();
        $this->assertArrayHasKey('sales', $response);
    }

    public function test_get_sales_with_date_filter_includes_fixture_sale(): void
    {
        $this->client->request(
            'GET',
            '/api/v1/sales?date=2026-04-20',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->cajeroToken]
        );

        $this->assertStatusCode(200);
        $response = $this->responseJson();
        $this->assertArrayHasKey('sales', $response);
        $this->assertNotEmpty($response['sales']);
    }

    public function test_get_sales_without_token_returns_401(): void
    {
        $this->client->request('GET', '/api/v1/sales');

        $this->assertStatusCode(401);
    }


    public function test_create_sale_line_returns_201(): void
    {
        $newLineId = 'c0000000-0000-0000-0000-000000000021';

        $this->jsonRequest(
            'PUT',
            '/api/v1/sale_line/' . $newLineId,
            [
                'product'  => [
                    'id'    => IntegrationFixtures::PRODUCT_ID,
                    'name'  => 'Leche Entera',
                    'price' => IntegrationFixtures::PRODUCT_PRICE,
                    'ean'   => IntegrationFixtures::PRODUCT_EAN,
                    'tax'   => [
                        'id'      => IntegrationFixtures::TAX_ID,
                        'name'    => 'IVA 21%',
                        'percent' => IntegrationFixtures::TAX_PCT,
                    ],
                ],
                'quantity' => 2,
            ],
            $this->cajeroToken
        );

        $this->assertStatusCode(201);
    }

    public function test_create_sale_line_missing_params_returns_400(): void
    {
        $this->jsonRequest(
            'PUT',
            '/api/v1/sale_line/c0000000-0000-0000-0000-000000000022',
            [],
            $this->cajeroToken
        );

        $this->assertStatusCode(400);
    }


    public function test_finish_open_sale_returns_201(): void
    {
        $this->jsonRequest(
            'PATCH',
            '/api/v1/sale_finish/' . self::OPEN_SALE_ID,
            ['payMethod' => 'cash'],
            $this->cajeroToken
        );

        $this->assertStatusCode(201);
    }

    public function test_finish_sale_not_found_returns_404(): void
    {
        $this->jsonRequest(
            'PATCH',
            '/api/v1/sale_finish/00000000-0000-0000-0000-000000000000',
            ['payMethod' => 'cash'],
            $this->cajeroToken
        );

        $this->assertStatusCode(404);
    }

    public function test_finish_sale_missing_pay_method_returns_400(): void
    {
        $this->jsonRequest(
            'PATCH',
            '/api/v1/sale_finish/' . self::OPEN_SALE_ID,
            [],
            $this->cajeroToken
        );

        $this->assertStatusCode(400);
    }


    public function test_delete_open_sale_returns_200(): void
    {
        $this->jsonRequest(
            'DELETE',
            '/api/v1/sale/' . self::OPEN_SALE_ID,
            [],
            $this->cajeroToken
        );

        $this->assertStatusCode(200);
    }

    public function test_delete_finished_sale_returns_200(): void
    {
        $this->jsonRequest(
            'DELETE',
            '/api/v1/sale/' . IntegrationFixtures::SALE_ID,
            [],
            $this->cajeroToken
        );

        $this->assertStatusCode(200);
    }

    public function test_delete_sale_not_found_returns_200(): void
    {
        $this->jsonRequest(
            'DELETE',
            '/api/v1/sale/00000000-0000-0000-0000-000000000000',
            [],
            $this->cajeroToken
        );

        $this->assertStatusCode(200);
    }
}
