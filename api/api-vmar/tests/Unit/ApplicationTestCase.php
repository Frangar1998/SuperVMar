<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SuperVMar\Product\Domain\Entity\PriceHistory;
use SuperVMar\Product\Domain\Product;
use SuperVMar\Shared\Domain\ValueObject\Id;

/**
 * Base TestCase for application-layer unit tests.
 * Provides shared data-builder helpers used across Product, Sale and User handlers.
 */
abstract class ApplicationTestCase extends TestCase
{

    protected const string ID_PRODUCT  = '550e8400-e29b-41d4-a716-000000000001';
    protected const string ID_SALE     = '550e8400-e29b-41d4-a716-000000000002';
    protected const string ID_USER     = '550e8400-e29b-41d4-a716-000000000003';
    protected const string ID_USER_DATA = '550e8400-e29b-41d4-a716-000000000004';
    protected const string ID_ADDRESS  = '550e8400-e29b-41d4-a716-000000000005';
    protected const string ID_TAX      = '550e8400-e29b-41d4-a716-000000000020';
    protected const string ID_CATEGORY = '550e8400-e29b-41d4-a716-000000000030';
    protected const string ID_SUPPLIER = '550e8400-e29b-41d4-a716-000000000040';


    /**
     * Builds a real Product domain object usable in tests that cannot mock
     * the final Product class.
     */
    protected function buildProduct(
        string $id     = self::ID_PRODUCT,
        int    $stock  = 50,
    ): Product {
        return Product::fromArray([
            'id'           => $id,
            'name'         => 'Leche Entera',
            'price'        => 1.29,
            'ean'          => '1234567',
            'stock'        => $stock,
            'idTax'        => self::ID_TAX,
            'nameTax'      => 'IVA 21%',
            'percent'      => 21.0,
            'idCategory'   => self::ID_CATEGORY,
            'nameCategory' => 'Lácteos',
            'idSupplier'   => self::ID_SUPPLIER,
            'nameSupplier' => 'Proveedor Test',
            'active'       => 1,
            'priceHistory' => [],
            'image'        => null,
        ]);
    }

    /**
     * Returns a valid array for SaveProductCommand's tax parameter.
     */
    protected function taxArray(): array
    {
        return ['id' => self::ID_TAX, 'name' => 'IVA 21%', 'percent' => 21.0];
    }

    /**
     * Returns a valid array for SaveProductCommand's category parameter.
     */
    protected function categoryArray(): array
    {
        return ['id' => self::ID_CATEGORY, 'name' => 'Lácteos'];
    }

    /**
     * Returns a valid array for SaveProductCommand's supplier parameter.
     */
    protected function supplierArray(): array
    {
        return ['id' => self::ID_SUPPLIER, 'name' => 'Proveedor Test'];
    }


    /**
     * Returns the product sub-array used in SaveSaleLineCommand.
     */
    protected function saleProductArray(): array
    {
        return [
            'id'    => self::ID_PRODUCT,
            'name'  => 'Leche Entera',
            'price' => 1.29,
            'ean'   => '1234567',
            'tax'   => $this->taxArray(),
        ];
    }


    /**
     * Returns the userData sub-array for SaveUserCommand.
     */
    protected function userDataArray(): array
    {
        return [
            'id'      => self::ID_USER_DATA,
            'name'    => 'Test',
            'surname' => 'User',
            'email'   => 'test@example.com',
            'phone'   => '612345678',
            'address' => [
                'id'         => self::ID_ADDRESS,
                'name'       => 'Calle Test',
                'postalCode' => '28001',
                'city'       => 'Madrid',
                'number'     => '1',
                'province'   => 'Madrid',
            ],
        ];
    }

    /** Valid plain-text password satisfying all PasswordRequirements. */
    protected const string VALID_PASSWORD     = 'TestPassword1_';
    /** A different valid password for "new password" scenarios. */
    protected const string NEW_VALID_PASSWORD = 'NewPassword2@X';
}
