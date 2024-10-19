<?php

namespace Jgrasp\PrestashopMigrationPlugin\Model\Product;

use Jgrasp\PrestashopMigrationPlugin\Attribute\Field;
use Jgrasp\PrestashopMigrationPlugin\Model\ModelInterface;
use Jgrasp\PrestashopMigrationPlugin\Model\ToggleableTrait;
use Jgrasp\PrestashopMigrationPlugin\Model\TranslationModelTrait;
use Jgrasp\PrestashopMigrationPlugin\Model\UrlModelTrait;

class ProductBrandProductModel implements ModelInterface
{
    use TranslationModelTrait;
    use UrlModelTrait;
    use ToggleableTrait;

    #[Field(source: 'id_product', target: 'prestashopId', id: true)]
    public int $id;

    #[Field(source: 'id_manufacturer')]
    public int $manufacturerID;
}
