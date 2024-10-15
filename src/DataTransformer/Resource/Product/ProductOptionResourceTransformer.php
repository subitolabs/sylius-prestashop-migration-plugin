<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\DataTransformer\Resource\Product;

use Jgrasp\PrestashopMigrationPlugin\DataTransformer\Resource\ResourceTransformerInterface;
use Jgrasp\PrestashopMigrationPlugin\DataTransformer\StringUtils;
use Jgrasp\PrestashopMigrationPlugin\Model\LocaleFetcher;
use Jgrasp\PrestashopMigrationPlugin\Model\ModelInterface;
use Sylius\Component\Product\Model\ProductOption;
use Sylius\Component\Resource\Model\ResourceInterface;

class ProductOptionResourceTransformer implements ResourceTransformerInterface
{
    private ResourceTransformerInterface $transformer;

    private LocaleFetcher $localeFetcher;

    public function __construct(ResourceTransformerInterface $transformer, LocaleFetcher $localeFetcher)
    {
        $this->transformer   = $transformer;
        $this->localeFetcher = $localeFetcher;
    }

    public function transform(ModelInterface $model): ResourceInterface
    {
        /**
         * @var ProductOption $resource
         */
        $resource = $this->transformer->transform($model);

        foreach ($this->localeFetcher->getLocales() as $locale) {
            $resource->setCurrentLocale($locale->getCode());
            $resource->setFallbackLocale($locale->getCode());

            $name = $model->name[$locale->getCode()];

            $resource->setName($name);

            if (null === $resource->getId() && null === $resource->getCode()) {
                $resource->setCode(StringUtils::stringToCode(sprintf('%s_%s', $resource->getName(), $model->id)));
            }
        }

        return $resource;
    }
}
