<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\DataCollector;

interface DataCollectorInterface
{
    public function collect(array $critiera, int $limit, int $offset): array;

    public function size(array $criteria = []): int;
}
