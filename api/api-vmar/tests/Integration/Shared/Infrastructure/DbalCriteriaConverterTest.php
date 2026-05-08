<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Integration\Shared\Infrastructure;

use SuperVMar\App\Tests\Fixtures\IntegrationFixtures;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\FieldName;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\FilterField;
use SuperVMar\Shared\Domain\Criteria\FilterOperator;
use SuperVMar\Shared\Domain\Criteria\Filters;
use SuperVMar\Shared\Domain\Criteria\FilterValue;
use SuperVMar\Shared\Domain\Criteria\Order;
use SuperVMar\Shared\Domain\Criteria\OrderBy;
use SuperVMar\Shared\Domain\Criteria\OrderType;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;

final class DbalCriteriaConverterTest extends DbalTestCase
{
    private DbalCriteriaConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = self::getContainer()->get(DbalCriteriaConverter::class);

        $this->connection->insert('category', ['id' => IntegrationFixtures::CATEGORY_ID, 'name' => 'Lácteos']);
        $this->connection->insert('category', ['id' => 'b1000000-0000-0000-0000-000000000001', 'name' => 'Bebidas']);
    }

    private function runQuery(Criteria $criteria): array
    {
        $qb = $this->connection->createQueryBuilder();
        return $this->converter->convert('category', $criteria, $qb)
            ->executeQuery()
            ->fetchAllAssociative();
    }


    public function test_empty_criteria_returns_all_rows(): void
    {
        $rows = $this->runQuery(new Criteria());

        $this->assertGreaterThanOrEqual(2, count($rows));
    }


    public function test_filter_by_exact_name(): void
    {
        $criteria = new Criteria(
            filters: new Filters([
                new Filter(
                    new FilterField(TableNames::TABLE_CATEGORY, new FieldName('name')),
                    FilterOperator::EQUAL,
                    new FilterValue('Lácteos'),
                ),
            ])
        );

        $rows = $this->runQuery($criteria);

        $this->assertCount(1, $rows);
        $this->assertSame('Lácteos', $rows[0]['name']);
    }


    public function test_two_filters_narrowing_result(): void
    {
        $criteria = new Criteria(
            filters: new Filters([
                new Filter(
                    new FilterField(TableNames::TABLE_CATEGORY, new FieldName('name')),
                    FilterOperator::EQUAL,
                    new FilterValue('Lácteos'),
                ),
                new Filter(
                    new FilterField(TableNames::TABLE_CATEGORY, new FieldName('id')),
                    FilterOperator::EQUAL,
                    new FilterValue(IntegrationFixtures::CATEGORY_ID),
                ),
            ])
        );

        $rows = $this->runQuery($criteria);

        $this->assertCount(1, $rows);
        $this->assertSame(IntegrationFixtures::CATEGORY_ID, $rows[0]['id']);
    }


    public function test_order_by_name_asc(): void
    {
        $criteria = new Criteria(
            order: Order::createAsc(new OrderBy('name'))
        );

        $rows = $this->runQuery($criteria);

        $this->assertGreaterThanOrEqual(2, count($rows));
        $this->assertLessThanOrEqual(0, strcmp($rows[0]['name'], $rows[1]['name']));
    }


    public function test_order_by_name_desc(): void
    {
        $criteria = new Criteria(
            order: Order::createDesc(new OrderBy('name'))
        );

        $rows = $this->runQuery($criteria);

        $this->assertGreaterThanOrEqual(2, count($rows));
        $this->assertGreaterThanOrEqual(0, strcmp($rows[0]['name'], $rows[1]['name']));
    }


    public function test_limit_restricts_result_count(): void
    {
        $criteria = new Criteria(limit: 1);

        $rows = $this->runQuery($criteria);

        $this->assertCount(1, $rows);
    }


    public function test_offset_skips_first_row(): void
    {
        $all = $this->runQuery(new Criteria(order: Order::createAsc(new OrderBy('name'))));
        $offset = $this->runQuery(new Criteria(order: Order::createAsc(new OrderBy('name')), offset: 1));

        $this->assertCount(count($all) - 1, $offset);
        $this->assertSame($all[1]['id'], $offset[0]['id']);
    }


    public function test_filter_with_no_match_returns_empty_array(): void
    {
        $criteria = new Criteria(
            filters: new Filters([
                new Filter(
                    new FilterField(TableNames::TABLE_CATEGORY, new FieldName('name')),
                    FilterOperator::EQUAL,
                    new FilterValue('NonExistent'),
                ),
            ])
        );

        $rows = $this->runQuery($criteria);

        $this->assertSame([], $rows);
    }
}
