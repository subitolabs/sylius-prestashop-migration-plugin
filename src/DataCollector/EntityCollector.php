<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\DataCollector;

use Jgrasp\PrestashopMigrationPlugin\Repository\EntityRepositoryInterface;

class EntityCollector implements DataCollectorInterface
{
    private EntityRepositoryInterface $repository;

    public function __construct(EntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function collect(array $critiera, int $limit, int $offset): array
    {
        return $this->repository->findAll($critiera, $limit, $offset);
    }

    public function size(array $criteria = []): int
    {
        return $this->repository->count($criteria);
    }
}
