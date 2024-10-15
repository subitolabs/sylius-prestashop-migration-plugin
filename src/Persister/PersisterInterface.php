<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\Persister;

use Sylius\Component\Resource\Model\ResourceInterface;

interface PersisterInterface
{
    public function persist(array $data): void;
}
