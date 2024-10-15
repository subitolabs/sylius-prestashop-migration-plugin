<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\Model\Product;

use Jgrasp\PrestashopMigrationPlugin\Attribute\Field;
use Jgrasp\PrestashopMigrationPlugin\Model\ModelInterface;

class ProductFeatureModel implements ModelInterface
{
    #[Field(source: 'id_feature')]
    public int $featureId;

    #[Field(source: 'id_feature_value')]
    public int $featureValueId;

    #[Field(source: 'id_product')]
    public int $productId;
}
