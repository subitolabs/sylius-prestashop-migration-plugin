<?php

namespace Jgrasp\PrestashopMigrationPlugin\Repository;

interface EntityRepositoryInterface
{
    public function find(int $id): array;

    public function findAll(array $critiera = [], int $limit = null, int $offset = null): array;

    public function findTranslations(int $id): array;

    public function count(array $criteria = []): int;

    public function getPrimaryKey(): ?string;
}
