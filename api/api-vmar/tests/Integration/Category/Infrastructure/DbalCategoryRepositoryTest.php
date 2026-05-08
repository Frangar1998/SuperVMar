<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Integration\Category\Infrastructure;

use SuperVMar\App\Tests\Fixtures\IntegrationFixtures;
use SuperVMar\App\Tests\Integration\Shared\Infrastructure\DbalTestCase;
use SuperVMar\Category\Domain\CategoryRepository;
use SuperVMar\Category\Domain\Category;
use SuperVMar\Category\Infrastructure\DbalCategoryRepository;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\FieldName;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\FilterField;
use SuperVMar\Shared\Domain\Criteria\FilterOperator;
use SuperVMar\Shared\Domain\Criteria\FilterValue;
use SuperVMar\Shared\Domain\Criteria\Filters;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;

final class DbalCategoryRepositoryTest extends DbalTestCase
{
    private const string CAT_ID   = 'b0000000-0000-0000-0000-000000000001';
    private const string CAT_NAME = 'Bebidas';

    private CategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(DbalCategoryRepository::class);
    }


    public function test_insert_persists_category_row(): void
    {
        $this->repository->insert($this->buildCategory());

        $row = $this->findById('category', self::CAT_ID);
        $this->assertNotFalse($row);
        $this->assertSame(self::CAT_NAME, $row['name']);
    }

    public function test_insert_throws_on_duplicate_id(): void
    {
        $cat = $this->buildCategory();
        $this->repository->insert($cat);

        $this->expectException(DuplicateItemException::class);
        $this->repository->insert($cat);
    }


    public function test_update_changes_category_name(): void
    {
        $this->repository->insert($this->buildCategory());
        $this->repository->update($this->buildCategory(name: 'Bebidas Frías'));

        $row = $this->findById('category', self::CAT_ID);
        $this->assertSame('Bebidas Frías', $row['name']);
    }


    public function test_delete_removes_category_row(): void
    {
        $this->repository->insert($this->buildCategory());
        $this->repository->delete(new Id(self::CAT_ID));

        $row = $this->findById('category', self::CAT_ID);
        $this->assertFalse($row);
    }


    public function test_search_returns_matching_category(): void
    {
        $this->repository->insert($this->buildCategory());

        $criteria   = new Criteria(new Filters([new Filter(
            new FilterField(TableNames::TABLE_CATEGORY, new FieldName('id')),
            FilterOperator::EQUAL,
            new FilterValue(self::CAT_ID)
        )]));
        $categories = $this->repository->searchByCriteria($criteria);

        $this->assertCount(1, $categories);
    }

    public function test_search_throws_when_no_match(): void
    {
        $criteria = new Criteria(new Filters([new Filter(
            new FilterField(TableNames::TABLE_CATEGORY, new FieldName('id')),
            FilterOperator::EQUAL,
            new FilterValue(self::CAT_ID)
        )]));

        $this->expectException(ItemNotFoundException::class);
        $this->repository->searchByCriteria($criteria);
    }


    private function buildCategory(string $name = self::CAT_NAME): Category
    {
        return Category::fromArray(['id' => self::CAT_ID, 'name' => $name]);
    }
}
