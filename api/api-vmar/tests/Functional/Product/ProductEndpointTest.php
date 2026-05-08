<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Functional\Product;

use SuperVMar\App\Tests\Fixtures\IntegrationFixtures;
use SuperVMar\App\Tests\Functional\Shared\ApiTestCase;

/**
 * Functional tests for product CRUD endpoints.
 *
 * @covers \SuperVMar\App\Controller\Product\ProductsGetController
 * @covers \SuperVMar\App\Controller\Product\ProductGetController
 * @covers \SuperVMar\App\Controller\Product\ProductPostController
 * @covers \SuperVMar\App\Controller\Product\ProductDeleteController
 * @covers \SuperVMar\App\Controller\Product\ProductReceiveStockPutController
 */
final class ProductEndpointTest extends ApiTestCase
{
    private IntegrationFixtures $fixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixtures = new IntegrationFixtures($this->connection);
        $this->connection->executeStatement("DELETE FROM price_history WHERE idProduct IN (?, ?)", [IntegrationFixtures::PRODUCT_ID, 'c0000000-0000-0000-0000-000000000001']);
        $this->connection->executeStatement("DELETE FROM product WHERE idSupplier = ?", [IntegrationFixtures::SUPPLIER_ID]);
        $this->connection->executeStatement("DELETE FROM supplier WHERE id = ?", [IntegrationFixtures::SUPPLIER_ID]);
        $this->connection->executeStatement("DELETE FROM category WHERE id = ?", [IntegrationFixtures::CATEGORY_ID]);
        $this->connection->executeStatement("DELETE FROM tax WHERE id = ?", [IntegrationFixtures::TAX_ID]);
        $this->fixtures->loadCatalog();
        $this->fixtures->loadProduct();
    }

    protected function tearDown(): void
    {
        $this->connection->executeStatement('DELETE FROM price_history WHERE idProduct IN (?, ?)', [IntegrationFixtures::PRODUCT_ID, 'c0000000-0000-0000-0000-000000000001']);
        $this->connection->executeStatement('DELETE FROM product WHERE idSupplier = ?', [IntegrationFixtures::SUPPLIER_ID]);
        $this->connection->executeStatement('DELETE FROM supplier WHERE id = ?', [IntegrationFixtures::SUPPLIER_ID]);
        $this->connection->executeStatement('DELETE FROM category WHERE id = ?', [IntegrationFixtures::CATEGORY_ID]);
        $this->connection->executeStatement('DELETE FROM tax WHERE id = ?', [IntegrationFixtures::TAX_ID]);
        parent::tearDown();
    }


    public function test_get_products_as_admin_returns_200_with_list(): void
    {
        $this->jsonRequest('GET', '/api/v1/products', [], $this->adminToken);

        $this->assertStatusCode(200);
        $response = $this->responseJson();
        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('id', $response[0]);
    }

    public function test_get_products_as_cajero_returns_403(): void
    {
        $this->jsonRequest('GET', '/api/v1/products', [], $this->cajeroToken);

        $this->assertStatusCode(403);
    }

    public function test_get_products_without_token_returns_401(): void
    {
        $this->client->request('GET', '/api/v1/products');

        $this->assertStatusCode(401);
    }


    public function test_get_product_by_ean_as_cajero_returns_200(): void
    {
        $this->client->request(
            'GET',
            '/api/v1/product?field=ean&value=' . IntegrationFixtures::PRODUCT_EAN,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->cajeroToken]
        );

        $this->assertStatusCode(200);
        $response = $this->responseJson();
        $this->assertArrayHasKey('id', $response);
        $this->assertSame(IntegrationFixtures::PRODUCT_ID, $response['id']);
    }

    public function test_get_product_not_found_returns_404(): void
    {
        $this->client->request(
            'GET',
            '/api/v1/product?field=id&value=00000000-0000-0000-0000-000000000000',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken]
        );

        $this->assertStatusCode(404);
    }


    public function test_create_product_as_admin_returns_201(): void
    {
        $newProductId = 'c0000000-0000-0000-0000-000000000001';

        $this->client->request(
            'POST',
            '/api/v1/product/' . $newProductId,
            [
                'data' => json_encode([
                    'name'     => 'Aceite Oliva',
                    'price'    => 3.99,
                    'ean'      => '8410076515527',
                    'stock'    => 10,
                    'tax'      => ['id' => IntegrationFixtures::TAX_ID, 'name' => 'IVA 21%', 'percent' => IntegrationFixtures::TAX_PCT],
                    'category' => ['id' => IntegrationFixtures::CATEGORY_ID, 'name' => 'Lácteos'],
                    'supplier' => ['id' => IntegrationFixtures::SUPPLIER_ID, 'name' => 'Proveedor Test'],
                    'active'   => 1,
                ]),
            ],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken]
        );

        $this->assertStatusCode(201);
    }

    public function test_create_product_missing_data_param_returns_400(): void
    {
        $this->client->request(
            'POST',
            '/api/v1/product/c0000000-0000-0000-0000-000000000002',
            [],  // no 'data' field
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken]
        );

        $this->assertStatusCode(400);
    }


    public function test_delete_product_as_admin_returns_200(): void
    {
        $this->connection->executeStatement(
            'UPDATE product SET active = 0, stock = 0 WHERE id = ?',
            [IntegrationFixtures::PRODUCT_ID]
        );

        $this->jsonRequest(
            'DELETE',
            '/api/v1/product/' . IntegrationFixtures::PRODUCT_ID,
            [],
            $this->adminToken
        );

        $this->assertStatusCode(200);
    }

    public function test_delete_product_not_found_returns_404(): void
    {
        $this->jsonRequest(
            'DELETE',
            '/api/v1/product/00000000-0000-0000-0000-000000000000',
            [],
            $this->adminToken
        );

        $this->assertStatusCode(404);
    }


    public function test_receive_stock_as_admin_returns_200(): void
    {
        $this->jsonRequest(
            'PUT',
            '/api/v1/product/' . IntegrationFixtures::PRODUCT_ID . '/receive-stock',
            ['quantity' => 20],
            $this->adminToken
        );

        $this->assertStatusCode(200);
    }

    public function test_receive_stock_missing_quantity_returns_400(): void
    {
        $this->jsonRequest(
            'PUT',
            '/api/v1/product/' . IntegrationFixtures::PRODUCT_ID . '/receive-stock',
            [],
            $this->adminToken
        );

        $this->assertStatusCode(400);
    }
}
