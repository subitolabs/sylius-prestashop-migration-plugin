<?php

namespace Jgrasp\PrestashopMigrationPlugin\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class EntityRepository implements EntityRepositoryInterface
{
    private Connection $connection;

    private string $_prefix;

    private string $_entity;

    private ?string $_primaryKey;

    public function __construct(string $_entity, string $_prefix, ?string $_primaryKey, Connection $connection)
    {
        $this->_entity     = $_entity;
        $this->_prefix     = $_prefix;
        $this->_primaryKey = $_primaryKey;
        $this->connection  = $connection;
    }

    public function find(int $id): array
    {
        $query = $this->createQueryBuilder();

        $query
            ->select('*')
            ->from($this->getTable())
            ->where($query->expr()->eq($this->getPrimaryKey(), $id));

        $result = $this->connection->executeQuery($query)->fetchAssociative();

        return false !== $result ? $result : [];
    }

    public function findTranslations(int $id): array
    {
        $query = $this->createQueryBuilder();

        $query
            ->select('*')
            ->from($this->getTableTranslation())
            ->where($query->expr()->eq($this->getPrimaryKey(), $id));

        return $this->connection->executeQuery($query)->fetchAllAssociative();
    }

    public function findAll(array $criteria = [], int $limit = null, int $offset = null): array
    {
        $query = $this
            ->createQueryBuilder()
            ->select('*')
            ->from($this->getTable());

        if (!empty($criteria)) {
            foreach ($criteria as $field => $filter) {
                $query->where(
                    $query->expr()->eq($field, $query->expr()->literal($filter))
                );
            }
        }

        if (null !== $limit && null !== $offset) {
            $query
                ->setMaxResults($limit)
                ->setFirstResult($offset);
        }

        return $this->connection->executeQuery($query->getSQL())->fetchAllAssociative();
    }

    public function count(array $criteria = []): int
    {
        $pk = $this->getPrimaryKey();

        $query = $this->createQueryBuilder();
        $query
            ->select(empty($pk) ? 'COUNT(*)' : sprintf('COUNT(%s)', $this->getPrimaryKey()))
            ->from($this->getTable(), '_table');

        if (!empty($criteria)) {
            foreach ($criteria as $field => $filter) {
                $query = $query->where(
                    $query->expr()->eq($field, $query->expr()->literal($filter))
                );
            }
        }

        return (int) $this->connection->executeQuery($query)->fetchOne();
    }

    public function getPrimaryKey(): ?string
    {
        return $this->_primaryKey;
    }

    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    protected function getTable(): string
    {
        return $this->_prefix . $this->_entity;
    }

    protected function getTableAlias(): string
    {
        return $this->getTable();
    }

    protected function getTableTranslation(): string
    {
        return sprintf('%s_%s', $this->getTable(), 'lang');
    }

    protected function getTableChannel(): string
    {
        return sprintf('%s_%s', $this->getTable(), 'shop');
    }

    protected function getPrefix(): string
    {
        return $this->_prefix;
    }

    protected function getConnection(): Connection
    {
        return $this->connection;
    }
}
