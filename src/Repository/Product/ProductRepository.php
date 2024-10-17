<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\Repository\Product;

use Jgrasp\PrestashopMigrationPlugin\Repository\EntityRepository;

use function Doctrine\DBAL\Query\QueryBuilder;

class ProductRepository extends EntityRepository
{
    public function findAll(array $criteria = [], int $limit = null, int $offset = null): array
    {
        $query = $this
            ->createQueryBuilder()
            ->select('*')
            ->from($this->getTable(), '_table');

        if (!empty($criteria)) {
            foreach ($criteria as $field => $filter) {
                if ($field === 'category') {
                    $query = $query->innerJoin('_table', $this->getPrefix() . 'category_product', 'cat_pro', 'cat_pro.id_product = _table.id_product');
                    $query = $query->where(
                        $query->expr()->eq('cat_pro.id_category', $query->expr()->literal($filter))
                    );
                } else {
                    $query = $query->where(
                        $query->expr()->eq($field, $query->expr()->literal($filter))
                    );
                }
            }
        }

        if (null !== $limit && null !== $offset) {
            $query
                ->setMaxResults($limit)
                ->setFirstResult($offset);
        }

        return $this->getConnection()->executeQuery($query->getSQL())->fetchAllAssociative();
    }

    public function count(array $criteria = []): int
    {
        $query = $this->createQueryBuilder();
        $query
            ->select(sprintf('COUNT(_table.%s)', $this->getPrimaryKey()))
            ->from($this->getTable(), '_table');

        if (!empty($criteria)) {
            foreach ($criteria as $field => $filter) {
                if ($field === 'category') {
                    $query = $query->innerJoin('_table', $this->getPrefix() . 'category_product', 'cat_pro', 'cat_pro.id_product = _table.id_product');
                    $query = $query->where(
                        $query->expr()->eq('cat_pro.id_category', $query->expr()->literal($filter))
                    );
                } else {
                    $query = $query->where(
                        $query->expr()->eq($field, $query->expr()->literal($filter))
                    );
                }
            }
        }

        return (int) $this->getConnection()->executeQuery($query->getSQL())->fetchOne();
    }

    public function findByReference(string $reference): array
    {
        $query = $this->createQueryBuilder();

        $query
            ->select('*')
            ->from($this->getTable())
            ->where($query->expr()->like('reference', $query->expr()->literal($reference)));

        return $this->getConnection()->executeQuery($query->getSQL())->fetchAllAssociative();
    }

    public function findBySlug(string $slug): array
    {
        $query = $this->createQueryBuilder();

        $query
            ->select('*')
            ->from($this->getTableTranslation())
            ->where($query->expr()->like('link_rewrite', $query->expr()->literal($slug)));

        return $this->getConnection()->executeQuery($query->getSQL())->fetchAllAssociative();
    }

    public function getCategories(int $productId): array
    {
        $query = $this->createQueryBuilder();

        $query
            ->select('*')
            ->from($this->getPrefix() . 'category_product')
            ->where($query->expr()->eq('id_product', $productId));

        return $this->getConnection()->executeQuery($query->getSQL())->fetchAllAssociative();
    }

    public function getShops(int $productId): array
    {
        $query = $this->createQueryBuilder();

        $query
            ->select('*')
            ->from($this->getTableChannel())
            ->where($query->expr()->eq('id_product', $productId));

        return $this->getConnection()->executeQuery($query->getSQL())->fetchAllAssociative();
    }

    public function getImages(int $productId): array
    {
        $query = $this->createQueryBuilder();

        $query
            ->select('*')
            ->from($this->getPrefix() . 'image')
            ->where($query->expr()->eq('id_product', $productId))
            ->orderBy('position', 'ASC');

        return $this->getConnection()->executeQuery($query->getSQL())->fetchAllAssociative();
    }

    public function getPriceByShopId(int $productId, int $shopId): float
    {
        $query = $this->createQueryBuilder();

        $query
            ->select('price')
            ->from($this->getTableChannel())
            ->where($query->expr()->eq('id_product', $productId))
            ->andWhere($query->expr()->eq('id_shop', $shopId));

        return (float) $this->getConnection()->executeQuery($query->getSQL())->fetchOne();
    }
}
