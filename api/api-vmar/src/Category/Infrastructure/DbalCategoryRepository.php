<?php

namespace SuperVMar\Category\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\Category\Domain\Categories;
use SuperVMar\Category\Domain\Category;
use SuperVMar\Category\Domain\CategoryRepository;
use SuperVMar\Category\Infrastructure\Dao\DbalProductDao;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use Throwable;

final readonly class DbalCategoryRepository implements CategoryRepository
{
    private const string TABLE_CATEGORY = TableNames::TABLE_CATEGORY->value;

    public function __construct(
        private Connection $connection,
        private DbalCriteriaConverter $dbalCriteriaConverter,
        private DbalProductDao $dbalProductDao
    )
    {
    }

    /**
     * @throws DuplicateItemException
     * @throws InternalErrorException
     */
    public function insert(Category $category): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->insert(self::TABLE_CATEGORY)
                ->values(
                    [
                        'id' => ':id',
                        'name' => ':name',
                    ])
                ->setParameters(
                    [
                        'id' => $category->id(),
                        'name' => $category->name(),
                    ])
                ->executeStatement();

        } catch (UniqueConstraintViolationException) {
            throw new DuplicateItemException(Category::class, $category->id());
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(Category $category): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE_CATEGORY)
                ->set('name', ':name')
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $category->id(),
                        'name' => $category->name(),
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function delete(Id $idCategory): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_CATEGORY)
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $idCategory,
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): Categories
    {
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $categories = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$categories) {
            throw new ItemNotFoundException(Category::class, $criteria->filters()?->toArray() ?? []);
        }

        return Categories::fromArray($categories);
    }

    /**
     * @throws ItemNotFoundException
     * @throws InternalErrorException
     */
    public function checkCategorizedProductsExists(Id $idCategory): void
    {
        $this->dbalProductDao->checkCategorizedProductsExists($idCategory);
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE_CATEGORY,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}