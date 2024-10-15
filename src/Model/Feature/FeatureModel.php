<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\Model\Feature;

use Jgrasp\PrestashopMigrationPlugin\Attribute\Field;
use Jgrasp\PrestashopMigrationPlugin\Model\ModelInterface;

class FeatureModel implements ModelInterface
{
    #[Field(source: 'id_feature', target: 'prestashopId', id: true)]
    public int $id;

    #[Field(source: 'position', target: 'position')]
    public int $position;

    #[Field(source: 'name', translatable: true)]
    public array $name;
}
