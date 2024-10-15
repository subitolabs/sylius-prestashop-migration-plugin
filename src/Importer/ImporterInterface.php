<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\Importer;

interface ImporterInterface
{
    public function import(array $criteria = [], callable $callable = null): void;

    public function size(array $criteria = [], ?int $limit = null): int;

    public function getName(): string;
}
