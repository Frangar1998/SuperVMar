<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Functional\Authentication;

use SuperVMar\App\Tests\Fixtures\UserFixtures;
use SuperVMar\App\Tests\Functional\Shared\ApiTestCase;

/**
 * Functional tests for the login endpoint and JWT authentication.
 *
 * @covers \SuperVMar\App\Controller\Login\LoginPostController
 */
final class LoginTest extends ApiTestCase
{
    public function test_admin_login_returns_202_with_token_and_fields(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['username' => 'test_admin', 'password' => UserFixtures::PASSWORD])
        );

        $this->assertStatusCode(202);

        $response = $this->responseJson();
        $this->assertArrayHasKey('token', $response);
        $this->assertNotEmpty($response['token']);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('username', $response);
        $this->assertSame('test_admin', $response['username']);
        $this->assertArrayHasKey('isAdmin', $response);
        $this->assertSame(1, $response['isAdmin']);
        $this->assertArrayHasKey('roles', $response);
        $this->assertContains('ROLE_ADMIN', $response['roles']);
    }

    public function test_cajero_login_returns_202_with_job_field(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['username' => 'test_cajero', 'password' => UserFixtures::PASSWORD])
        );

        $this->assertStatusCode(202);

        $response = $this->responseJson();
        $this->assertSame('test_cajero', $response['username']);
        $this->assertSame(0, $response['isAdmin']);
        $this->assertNotNull($response['job']);
        $this->assertContains('ROLE_CAJERO', $response['roles']);
    }

    public function test_login_missing_password_returns_400(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['username' => 'test_admin'])
        );

        $this->assertStatusCode(400);
    }

    public function test_login_missing_username_returns_400(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['password' => UserFixtures::PASSWORD])
        );

        $this->assertStatusCode(400);
    }

    public function test_protected_route_without_token_returns_401(): void
    {
        $this->client->request('GET', '/api/v1/products');

        $this->assertStatusCode(401);
    }

    public function test_admin_token_obtained_in_setup_grants_access_to_products(): void
    {
        $this->jsonRequest('GET', '/api/v1/products', [], $this->adminToken);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertNotSame(401, $statusCode, 'Admin token should not be rejected (401)');
        $this->assertNotSame(403, $statusCode, 'Admin token should have ROLE_ENCARGADO access (403)');
    }
}
