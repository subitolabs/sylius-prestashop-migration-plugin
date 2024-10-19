<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\DataTransformer\Resource\Product;

use Jgrasp\PrestashopMigrationPlugin\DataTransformer\Resource\ResourceTransformerInterface;
use Jgrasp\PrestashopMigrationPlugin\DataTransformer\StringUtils;
use Jgrasp\PrestashopMigrationPlugin\Model\ModelInterface;
use Jgrasp\PrestashopMigrationPlugin\Model\Product\ProductBrandModel;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class ProductBrandResourceTransformer implements ResourceTransformerInterface
{
    private ResourceTransformerInterface $transformer;

    public function __construct(ResourceTransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @param ProductBrandModel $model
     * @return ResourceInterface
     */
    public function transform(ModelInterface $model): ResourceInterface
    {
        /**
         * @var CodeAwareInterface $resource
         */
        $resource = $this->transformer->transform($model);
        $resource->setCode(sprintf('%s_%d', StringUtils::stringToCode($model->name), $model->id));

        return $resource;
    }
}
