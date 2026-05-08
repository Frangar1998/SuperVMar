<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Functional\Shared;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use SuperVMar\App\Tests\Fixtures\UserFixtures;
use SuperVMar\Authentication\Infrastructure\Symfony\SecurityUser;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ApiTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected Connection $connection;

    /**
     * JWT token for admin user (username: test_admin, password: Test1234!)
     */
    protected string $adminToken;

    /**
     * JWT token for cajero worker (username: test_cajero, password: Test1234!)
     */
    protected string $cajeroToken;

    protected function setUp(): void
    {
        parent::setUp();
        static::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->connection = static::getContainer()->get(Connection::class);

        $this->cleanUserFixtures();

        $fixtures = new UserFixtures($this->connection);
        $fixtures->load($this->createStub(ObjectManager::class));

        $jwtManager = static::getContainer()->get(JWTTokenManagerInterface::class);

        $admin = new SecurityUser(UserFixtures::ADMIN_ID, 'test_admin', '', 1, null);
        $this->adminToken = $jwtManager->create($admin);

        $cajero = new SecurityUser(UserFixtures::CAJERO_ID, 'test_cajero', '', 0, 'cajero test');
        $this->cajeroToken = $jwtManager->create($cajero);
    }

    protected function tearDown(): void
    {
        $this->cleanUserFixtures();
        parent::tearDown();
    }

    /**
     * Deletes all rows inserted by UserFixtures so setUp can re-insert them cleanly.
     */
    private function cleanUserFixtures(): void
    {
        $this->connection->executeStatement(
            'DELETE FROM worker_allocation WHERE idUser IN (?, ?)',
            [UserFixtures::ADMIN_ID, UserFixtures::CAJERO_ID]
        );
        $this->connection->executeStatement(
            'DELETE FROM supermarket WHERE id = ?',
            [UserFixtures::SUPERMARKET_ID]
        );
        $this->connection->executeStatement(
            'DELETE FROM user WHERE id IN (?, ?)',
            [UserFixtures::ADMIN_ID, UserFixtures::CAJERO_ID]
        );
        $this->connection->executeStatement(
            'DELETE FROM user_data WHERE id IN (?, ?)',
            [UserFixtures::ADMIN_DATA_ID, UserFixtures::CAJERO_DATA_ID]
        );
        $this->connection->executeStatement(
            'DELETE FROM job WHERE id = ?',
            [UserFixtures::JOB_CAJERO_ID]
        );
        $this->connection->executeStatement(
            'DELETE FROM address WHERE id = ?',
            [UserFixtures::ADDRESS_ID]
        );
    }

    /**
     * Obtains a JWT token via the login endpoint.
     * Used only in LoginTest to test the actual endpoint; other tests use
     * the programmatically-generated tokens from setUp.
     */
    protected function fetchJwtToken(string $username, string $password): string
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['username' => $username, 'password' => $password])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);

        return $data['token'] ?? throw new \RuntimeException(
            "Login failed for user '$username': " . $this->client->getResponse()->getContent()
        );
    }

    /**
     * Performs an authenticated JSON request.
     *
     * @param array<string, mixed> $body
     */
    protected function jsonRequest(
        string  $method,
        string  $uri,
        array   $body = [],
        ?string $token = null,
    ): void {
        $headers = ['CONTENT_TYPE' => 'application/json'];
        if ($token !== null) {
            $headers['HTTP_AUTHORIZATION'] = "Bearer $token";
        }

        $this->client->request(
            $method,
            $uri,
            [],
            [],
            $headers,
            $body !== [] ? json_encode($body) : '{}',
        );
    }

    /**
     * Decodes the current response body as JSON.
     *
     * @return array<string, mixed>
     */
    protected function responseJson(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true) ?? [];
    }

    /**
     * Asserts the response status code.
     */
    protected function assertStatusCode(int $expected): void
    {
        $this->assertSame(
            $expected,
            $this->client->getResponse()->getStatusCode(),
            'Response body: ' . $this->client->getResponse()->getContent(),
        );
    }
}

