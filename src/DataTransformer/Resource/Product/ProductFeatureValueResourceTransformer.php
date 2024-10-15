<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\DataTransformer\Resource\Product;

use App\Entity\Product\ProductAttribute;
use App\Entity\Product\ProductAttributeValue;
use Jgrasp\PrestashopMigrationPlugin\DataTransformer\Resource\ResourceTransformerInterface;
use Jgrasp\PrestashopMigrationPlugin\Model\LocaleFetcher;
use Jgrasp\PrestashopMigrationPlugin\Model\ModelInterface;
use Jgrasp\PrestashopMigrationPlugin\Model\Product\ProductFeatureModel;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class ProductFeatureValueResourceTransformer implements ResourceTransformerInterface
{
    private LocaleFetcher $localeFetcher;

    private RepositoryInterface $productRepository;

    private RepositoryInterface $productAttributeRepository;

    public function __construct(
        LocaleFetcher $localeFetcher,
        RepositoryInterface $productRepository,
        RepositoryInterface $productAttributeRepository
    ) {
        $this->localeFetcher              = $localeFetcher;
        $this->productRepository          = $productRepository;
        $this->productAttributeRepository = $productAttributeRepository;
    }

    /**
     * @param ProductFeatureModel $model
     */
    public function transform(ModelInterface $model): array | null
    {
        /** @var ProductInterface|ChannelsAwareInterface|null $product */
        $product = $this->productRepository->findOneBy(['prestashopId' => $model->productId]);

        if (null === $product) {
            return null;
        }

        /** @var ProductAttribute|null $attribute */
        $attribute = $this->productAttributeRepository->findOneBy(['prestashopId' => $model->featureId]);

        if (null === $attribute) {
            return null;
        }

        $prestashopId = $model->productId * 1000000 + $model->featureId * 1000 + $model->featureValueId;

        return array_map(function (LocaleInterface $locale) use ($product, $attribute, $model, $prestashopId) {
            /**
             * @var ProductAttributeValue $resource
             */
            $resource = new ProductAttributeValue();

            $resource->setPrestashopId($prestashopId);
            $resource->setProduct($product);
            $resource->setAttribute($attribute);
            $resource->setValue(['prestashop_' . $model->featureValueId]);
            $resource->setLocaleCode($locale->getCode());

            return $resource;
        }, $this->localeFetcher->getLocales());
    }
}
