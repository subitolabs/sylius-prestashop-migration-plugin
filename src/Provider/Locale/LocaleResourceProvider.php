<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\Provider\Locale;

use ReflectionClass;
use Jgrasp\PrestashopMigrationPlugin\Attribute\PropertyAttributeAccessor;
use Jgrasp\PrestashopMigrationPlugin\Model\ModelInterface;
use Jgrasp\PrestashopMigrationPlugin\Provider\ResourceProviderInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class LocaleResourceProvider implements ResourceProviderInterface
{
    private RepositoryInterface $repository;

    private ResourceProviderInterface $provider;

    private PropertyAttributeAccessor $propertyAttributeAccessor;

    public function __construct(ResourceProviderInterface $provider, RepositoryInterface $repository, PropertyAttributeAccessor $propertyAttributeAccessor)
    {
        $this->provider                  = $provider;
        $this->repository                = $repository;
        $this->propertyAttributeAccessor = $propertyAttributeAccessor;
    }

    public function getResource(ModelInterface $model): ResourceInterface
    {
        $modelReflection = new ReflectionClass($model);
        $modelProperties = $modelReflection->getProperties();
        $localeCode      = null;

        foreach ($modelProperties as $property) {
            if ($property->getName() === 'code') {
                $localeCode = $property->getValue($model);
            }
        }

        if (!empty($localeCode)) {
            $resource = $this->repository->findOneBy(['code' => StringInflector::nameToCode($localeCode)]);
        }

        if (empty($resource)) {
            $resource = $this->provider->getResource($model);
        }

        return $resource;
    }
}
