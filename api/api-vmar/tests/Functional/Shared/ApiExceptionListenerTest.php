<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Functional\Shared;

use Symfony\Component\HttpFoundation\Response;

/**
 * Tests the ApiExceptionListener and ApiExceptionHttpStatusCodeMapping:
 * verifies that domain exceptions are translated to the correct HTTP status
 * codes and that the JSON response body contains `code` and `message` keys.
 */
final class ApiExceptionListenerTest extends ApiTestCase
{
    private const string TAX_ID      = 'f1000000-0000-0000-0000-000000000001';
    private const string CATEGORY_ID = 'f1000000-0000-0000-0000-000000000002';
    private const string SUPPLIER_ID = 'f1000000-0000-0000-0000-000000000003';

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanCatalog();
        $this->connection->insert('tax',      ['id' => self::TAX_ID,      'name' => 'IVA 21%', 'percent' => 21.0]);
        $this->connection->insert('category', ['id' => self::CATEGORY_ID, 'name' => 'Lácteos']);
        $this->connection->insert('supplier', ['id' => self::SUPPLIER_ID, 'name' => 'Prov Test', 'phone' => '600000000', 'email' => 'prov@test.com', 'contact' => 'Contacto']);
    }

    protected function tearDown(): void
    {
        $this->cleanCatalog();
        parent::tearDown();
    }

    private function cleanCatalog(): void
    {
        $this->connection->executeStatement(
            'DELETE ph FROM price_history ph INNER JOIN product p ON ph.idProduct = p.id WHERE p.idTax = ?',
            [self::TAX_ID]
        );
        $this->connection->executeStatement('DELETE FROM product WHERE idTax = ?', [self::TAX_ID]);
        $this->connection->executeStatement('DELETE FROM supplier WHERE id = ?', [self::SUPPLIER_ID]);
        $this->connection->executeStatement('DELETE FROM category WHERE id = ?', [self::CATEGORY_ID]);
        $this->connection->executeStatement('DELETE FROM tax WHERE id = ?', [self::TAX_ID]);
    }

    public function test_item_not_found_returns_404(): void
    {
        $unknownId = '00000000-0000-0000-0000-000000000000';
        $this->jsonRequest('GET', "/api/v1/sale/{$unknownId}", token: $this->adminToken);

        $this->assertStatusCode(Response::HTTP_NOT_FOUND);
        $body = $this->responseJson();
        $this->assertArrayHasKey('code', $body);
        $this->assertArrayHasKey('message', $body);
    }


    public function test_duplicate_item_returns_409(): void
    {
        $id1 = 'e0000000-0000-0000-0000-000000000001';
        $id2 = 'e0000000-0000-0000-0000-000000000002';
        $duplicateEan = '9991234500001';

        $productData = json_encode([
            'name'     => 'Prod Dup Test',
            'price'    => 1.0,
            'ean'      => $duplicateEan,
            'stock'    => 10,
            'tax'      => ['id' => self::TAX_ID, 'name' => 'IVA 21%', 'percent' => 21.0],
            'category' => ['id' => self::CATEGORY_ID, 'name' => 'Lácteos'],
            'supplier' => ['id' => self::SUPPLIER_ID, 'name' => 'Prov Test'],
            'active'   => 1,
        ]);

        $this->client->request(
            'POST',
            "/api/v1/product/{$id1}",
            ['data' => $productData],
            [],
            ['HTTP_AUTHORIZATION' => "Bearer {$this->adminToken}"]
        );
        $this->assertStatusCode(201);

        $this->client->request(
            'POST',
            "/api/v1/product/{$id2}",
            ['data' => $productData],
            [],
            ['HTTP_AUTHORIZATION' => "Bearer {$this->adminToken}"]
        );
        $this->assertStatusCode(Response::HTTP_CONFLICT);
        $body = $this->responseJson();
        $this->assertArrayHasKey('code', $body);
        $this->assertArrayHasKey('message', $body);
    }


    public function test_invalid_uuid_returns_400(): void
    {
        $this->jsonRequest('GET', '/api/v1/sale/not-a-uuid', token: $this->adminToken);

        $this->assertStatusCode(Response::HTTP_BAD_REQUEST);
        $body = $this->responseJson();
        $this->assertArrayHasKey('code', $body);
        $this->assertArrayHasKey('message', $body);
    }


    public function test_missing_token_returns_401(): void
    {
        $this->jsonRequest('GET', '/api/v1/sales');

        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);
    }


    public function test_non_admin_accessing_users_list_returns_403(): void
    {
        $this->jsonRequest('GET', '/api/v1/users', token: $this->cajeroToken);

        $this->assertStatusCode(Response::HTTP_FORBIDDEN);
    }


    public function test_error_response_contains_code_and_message_keys(): void
    {
        $unknownId = '00000000-0000-0000-0000-111111111111';
        $this->jsonRequest('GET', "/api/v1/sale/{$unknownId}", token: $this->adminToken);

        $this->assertStatusCode(Response::HTTP_NOT_FOUND);
        $body = $this->responseJson();
        $this->assertArrayHasKey('code', $body);
        $this->assertArrayHasKey('message', $body);
        $this->assertIsString($body['code']);
        $this->assertIsString($body['message']);
    }
}
