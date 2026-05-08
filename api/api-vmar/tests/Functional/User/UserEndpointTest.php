<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Functional\User;

use SuperVMar\App\Tests\Fixtures\UserFixtures;
use SuperVMar\App\Tests\Functional\Shared\ApiTestCase;

/**
 * Functional tests for user CRUD endpoints.
 *
 * @covers \SuperVMar\App\Controller\User\UsersGetController
 * @covers \SuperVMar\App\Controller\User\UserGetController
 * @covers \SuperVMar\App\Controller\User\UserPutController
 * @covers \SuperVMar\App\Controller\User\UserDeleteController
 * @covers \SuperVMar\App\Controller\User\UserPasswordPutController
 */
final class UserEndpointTest extends ApiTestCase
{
    /** New user IDs used in creation / deletion tests. */
    private const string NEW_USER_ID      = 'd0000000-0000-0000-0000-000000000001';
    private const string NEW_USER_DATA_ID = 'd0000000-0000-0000-0000-000000000002';
    private const string NEW_ADDRESS_ID   = 'd0000000-0000-0000-0000-000000000003';

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        foreach ([self::NEW_USER_ID] as $uid) {
            $this->connection->executeStatement('DELETE FROM worker_allocation WHERE idUser = ?', [$uid]);
            $this->connection->executeStatement('DELETE FROM user WHERE id = ?', [$uid]);
        }
        foreach ([self::NEW_USER_DATA_ID] as $udid) {
            $this->connection->executeStatement('DELETE FROM user_data WHERE id = ?', [$udid]);
        }
        foreach ([self::NEW_ADDRESS_ID] as $aid) {
            $this->connection->executeStatement('DELETE FROM address WHERE id = ?', [$aid]);
        }
        parent::tearDown();
    }

    public function test_get_users_as_admin_returns_200_with_list(): void
    {
        $this->jsonRequest('GET', '/api/v1/users', [], $this->adminToken);

        $this->assertStatusCode(200);
        $response = $this->responseJson();
        $this->assertArrayHasKey('users', $response);
        $this->assertNotEmpty($response['users']);
    }

    public function test_get_users_as_cajero_returns_403(): void
    {
        $this->jsonRequest('GET', '/api/v1/users', [], $this->cajeroToken);

        $this->assertStatusCode(403);
    }

    public function test_get_users_without_token_returns_401(): void
    {
        $this->client->request('GET', '/api/v1/users');

        $this->assertStatusCode(401);
    }


    public function test_get_user_by_id_as_admin_returns_200(): void
    {
        $this->jsonRequest('GET', '/api/v1/user/' . UserFixtures::ADMIN_ID, [], $this->adminToken);

        $this->assertStatusCode(200);
        $response = $this->responseJson();
        $this->assertArrayHasKey('id', $response);
        $this->assertSame(UserFixtures::ADMIN_ID, $response['id']);
        $this->assertSame('test_admin', $response['username']);
    }

    public function test_get_user_not_found_returns_404(): void
    {
        $this->jsonRequest(
            'GET',
            '/api/v1/user/00000000-0000-0000-0000-000000000000',
            [],
            $this->adminToken
        );

        $this->assertStatusCode(404);
    }


    public function test_create_user_returns_201(): void
    {
        $this->jsonRequest(
            'PUT',
            '/api/v1/user/' . self::NEW_USER_ID,
            [
                'username'       => 'new_functional_user',
                'userData'       => [
                    'id'      => self::NEW_USER_DATA_ID,
                    'name'    => 'Nuevo',
                    'surname' => 'Usuario',
                    'email'   => 'nuevo@test.com',
                    'phone'   => '600999888',
                    'address' => [
                        'id'         => self::NEW_ADDRESS_ID,
                        'name'       => 'Calle Nueva',
                        'postalCode' => '28002',
                        'city'       => 'Madrid',
                        'number'     => '5',
                        'province'   => 'Madrid',
                    ],
                ],
                'isAdmin'        => 0,
                'allocations'    => [],
                'password'       => 'NewPassword1!@#',
                'passwordRepeat' => 'NewPassword1!@#',
            ]
        );

        $this->assertStatusCode(201);
    }

    public function test_create_user_missing_username_returns_400(): void
    {
        $this->client->request(
            'PUT',
            '/api/v1/user/d0000000-0000-0000-0000-000000000010',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'userData'    => [],
                'isAdmin'     => 0,
                'allocations' => [],
            ])
        );

        $this->assertStatusCode(400);
    }


    public function test_delete_user_as_admin_returns_200(): void
    {
        $this->connection->insert('address', [
            'id'         => self::NEW_ADDRESS_ID,
            'name'       => 'Calle Borrar',
            'postalCode' => '28001',
            'city'       => 'Madrid',
            'number'     => '1',
            'province'   => 'Madrid',
        ]);
        $this->connection->insert('user_data', [
            'id'        => self::NEW_USER_DATA_ID,
            'name'      => 'Borrar',
            'surname'   => 'Me',
            'email'     => 'borrar@test.com',
            'phone'     => '600000099',
            'idAddress' => self::NEW_ADDRESS_ID,
        ]);
        $this->connection->insert('user', [
            'id'         => self::NEW_USER_ID,
            'username'   => 'user_to_delete',
            'password'   => 'x',
            'isAdmin'    => 0,
            'idUserData' => self::NEW_USER_DATA_ID,
        ]);

        $this->jsonRequest(
            'DELETE',
            '/api/v1/user/' . self::NEW_USER_ID,
            [],
            $this->adminToken
        );

        $this->assertStatusCode(200);
    }

    public function test_delete_user_not_found_returns_404(): void
    {
        $this->jsonRequest(
            'DELETE',
            '/api/v1/user/00000000-0000-0000-0000-000000000000',
            [],
            $this->adminToken
        );

        $this->assertStatusCode(404);
    }

    public function test_delete_user_as_cajero_returns_403(): void
    {
        $this->jsonRequest(
            'DELETE',
            '/api/v1/user/' . UserFixtures::ADMIN_ID,
            [],
            $this->cajeroToken
        );

        $this->assertStatusCode(403);
    }


    public function test_change_password_with_valid_data_returns_200(): void
    {
        $this->jsonRequest(
            'PUT',
            '/api/v1/user/' . UserFixtures::ADMIN_ID . '/change-password',
            [
                'currentPassword' => UserFixtures::PASSWORD,
                'password'        => 'ChangedPassword1!@',
                'passwordRepeat'  => 'ChangedPassword1!@',
            ],
            $this->adminToken
        );

        $this->assertStatusCode(200);
    }

    public function test_change_password_missing_params_returns_400(): void
    {
        $this->jsonRequest(
            'PUT',
            '/api/v1/user/' . UserFixtures::ADMIN_ID . '/change-password',
            ['currentPassword' => UserFixtures::PASSWORD],
            $this->adminToken
        );

        $this->assertStatusCode(400);
    }
}
