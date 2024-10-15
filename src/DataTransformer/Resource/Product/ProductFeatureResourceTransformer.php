<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\DataTransformer\Resource\Product;

use App\Entity\Product\ProductAttribute;
use Jgrasp\PrestashopMigrationPlugin\DataTransformer\Resource\ResourceTransformerInterface;
use Jgrasp\PrestashopMigrationPlugin\DataTransformer\StringUtils;
use Jgrasp\PrestashopMigrationPlugin\Model\LocaleFetcher;
use Jgrasp\PrestashopMigrationPlugin\Model\ModelInterface;
use Jgrasp\PrestashopMigrationPlugin\Repository\Product\ProductFeatureRepository;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

class ProductFeatureResourceTransformer implements ResourceTransformerInterface
{
    private ResourceTransformerInterface $transformer;

    private LocaleFetcher $localeFetcher;

    private ProductFeatureRepository $featureRepository;

    public function __construct(ResourceTransformerInterface $transformer, LocaleFetcher $localeFetcher, ProductFeatureRepository $featureRepository)
    {
        $this->transformer       = $transformer;
        $this->localeFetcher     = $localeFetcher;
        $this->featureRepository = $featureRepository;
    }

    public function transform(ModelInterface $model): ResourceInterface
    {
        /**
         * @var ProductAttribute $resource
         */
        $resource = $this->transformer->transform($model);

        $resource->setStorageType('text');

        foreach ($this->localeFetcher->getLocales() as $locale) {
            $resource->setCurrentLocale($locale->getCode());
            $resource->setFallbackLocale($locale->getCode());

            $name = $model->name[$locale->getCode()];

            $resource->setName($name);

            if (null === $resource->getId() && null === $resource->getCode()) {
                $resource->setCode(StringUtils::stringToCode(sprintf('%s_%s', $resource->getName(), $model->id)));
            }
        }

        $prestashopLocales = array_reduce($this->localeFetcher->getLocales(), function (array $accumulator, LocaleInterface $locale) {
            $accumulator[$locale->getPrestashopId()] = $locale;

            return $accumulator;
        }, []);

        $this->transformTypeList($resource, $prestashopLocales);

        return $resource;
    }

    /**
     * Fill JSON configuration field.
     */
    private function transformTypeList(ProductAttribute $resource, array $locales): void
    {
        $values = $this->featureRepository->getFeatureValues($resource->getPrestashopId());

        $valuesById = array_reduce($values, function (array $accumulator, array $v) {
            $accumulator[$v['id_feature_value']][] = $v;

            return $accumulator;
        }, []);

        $choices = [];

        foreach ($valuesById as $featureValueId => $featureValueLang) {
            $choices['prestashop_' . $featureValueId] = [];

            foreach ($featureValueLang as $v) {
                /** @var LocaleInterface */
                $locale = $locales[$v['id_lang']];

                $choices['prestashop_' . $featureValueId][$locale->getCode()] = $v['value'];
            }
        }

        $payload = [
            'multiple' => false,
            'choices'  => $choices,
        ];

        $resource->setType('select');
        $resource->setStorageType('json');
        $resource->setConfiguration($payload);
    }
}
