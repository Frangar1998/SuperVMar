<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Fixtures;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;

/**
 * Shared fixture data for integration (DBAL) tests.
 *
 * Provides deterministic rows for tax, category, supplier, product, sale
 * and a minimal user set (with address / user_data / user tables).
 *
 * IDs are prefixed with 'a0000000' to avoid collisions with:
 *   - UserFixtures  (prefix 'f0000000')
 *   - Unit test UUIDs (prefix '550e8400')
 */
final class IntegrationFixtures extends BaseFixtures
{
    public const string TAX_ID   = 'a0000000-0000-0000-0000-000000000001';
    public const float  TAX_PCT  = 21.0;

    public const string CATEGORY_ID = 'a0000000-0000-0000-0000-000000000002';

    public const string SUPPLIER_ID = 'a0000000-0000-0000-0000-000000000003';

    public const string PRODUCT_ID    = 'a0000000-0000-0000-0000-000000000010';
    public const string PRODUCT_EAN   = '5000112637939';
    public const float  PRODUCT_PRICE = 1.29;
    public const int    PRODUCT_STOCK = 50;

    public const string SALE_ID      = 'a0000000-0000-0000-0000-000000000020';
    public const string SALE_LINE_ID = 'a0000000-0000-0000-0000-000000000021';

    public const string ADDR_ID      = 'a0000000-0000-0000-0000-000000000030';
    public const string USER_DATA_ID = 'a0000000-0000-0000-0000-000000000031';
    public const string USER_ID      = 'a0000000-0000-0000-0000-000000000032';
    public const string SUPERMARKET_ID = 'a0000000-0000-0000-0000-000000000040';
    public const string JOB_ID       = 'a0000000-0000-0000-0000-000000000041';

    public const string PASSWORD = 'Test1234!@#$';

    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadCatalog();
        $this->loadUser();
    }


    /**
     * Inserts tax + category + supplier rows.
     * Call this in integration test setUp that needs product FK data.
     */
    public function loadCatalog(): void
    {
        $this->insert('tax', [
            'id'      => self::TAX_ID,
            'name'    => 'IVA 21%',
            'percent' => self::TAX_PCT,
        ]);

        $this->insert('category', [
            'id'   => self::CATEGORY_ID,
            'name' => 'Lácteos',
        ]);

        $this->insert('supplier', [
            'id'      => self::SUPPLIER_ID,
            'name'    => 'Proveedor Test',
            'phone'   => '600000000',
            'email'   => 'proveedor@test.com',
            'contact' => 'Contacto Test',
        ]);
    }

    /**
     * Inserts a product row (requires catalog rows to already exist).
     */
    public function loadProduct(): void
    {
        $this->insert('product', [
            'id'          => self::PRODUCT_ID,
            'name'        => 'Leche Entera',
            'price'       => self::PRODUCT_PRICE,
            'ean'         => self::PRODUCT_EAN,
            'stock'       => self::PRODUCT_STOCK,
            'idTax'       => self::TAX_ID,
            'idCategory'  => self::CATEGORY_ID,
            'idSupplier'  => self::SUPPLIER_ID,
            'active'      => 1,
            'image'       => null,
        ]);
    }

    /**
     * Inserts a finished sale row + one sale_line (requires product to exist).
     */
    public function loadSale(): void
    {
        $this->insert('sale', [
            'id'          => self::SALE_ID,
            'amount'      => 1.29,
            'taxes'       => 0.27,
            'totalAmount' => 1.56,
            'payMethod'   => 'cash',
            'date'        => '2026-04-21',
        ]);

        $this->insert('sale_line', [
            'id'        => self::SALE_LINE_ID,
            'idSale'    => self::SALE_ID,
            'idProduct' => self::PRODUCT_ID,
            'amount'    => 1.29,
            'quantity'  => 1,
        ]);
    }

    /**
     * Inserts address + user_data + user rows.
     */
    public function loadUser(): void
    {
        $hashed = $this->hashPassword(self::PASSWORD);

        $this->insert('address', [
            'id'         => self::ADDR_ID,
            'name'       => 'Calle Integración',
            'postalCode' => '28080',
            'city'       => 'Madrid',
            'number'     => '10',
            'province'   => 'Madrid',
        ]);

        $this->insert('user_data', [
            'id'        => self::USER_DATA_ID,
            'name'      => 'Integration',
            'surname'   => 'Tester',
            'email'     => 'integration@test.com',
            'phone'     => '699000001',
            'idAddress' => self::ADDR_ID,
        ]);

        $this->insert('user', [
            'id'         => self::USER_ID,
            'username'   => 'integration_user',
            'password'   => $hashed,
            'isAdmin'    => 0,
            'idUserData' => self::USER_DATA_ID,
        ]);

        $this->insert('supermarket', [
            'id'        => self::SUPERMARKET_ID,
            'name'      => 'Integration Supermarket',
            'email'     => 'sm@test.com',
            'idAddress' => self::ADDR_ID,
        ]);

        $this->insert('job', [
            'id'   => self::JOB_ID,
            'name' => 'cajero integración',
        ]);

        $this->insert('worker_allocation', [
            'idUser'        => self::USER_ID,
            'idSupermarket' => self::SUPERMARKET_ID,
            'idJob'         => self::JOB_ID,
        ]);
    }
}
