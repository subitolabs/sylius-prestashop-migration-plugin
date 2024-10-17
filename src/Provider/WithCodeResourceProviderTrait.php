<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\Provider;

use ReflectionClass;
use Jgrasp\PrestashopMigrationPlugin\Model\ModelInterface;
use Jgrasp\PrestashopMigrationPlugin\Provider\ResourceProviderInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

trait WithCodeResourceProviderTrait {
    private RepositoryInterface $repository;

    private ResourceProviderInterface $provider;

    public function __construct(ResourceProviderInterface $provider, RepositoryInterface $repository)
    {
        $this->provider                  = $provider;
        $this->repository                = $repository;
    }

    /**
     * Search existing sylius object by code.
     * Fallback to default getResource method.
     */
    public function getResource(ModelInterface $model): ResourceInterface
    {
        $modelReflection = new ReflectionClass($model);
        $modelProperties = $modelReflection->getProperties();
        $valueCode      = null;

        foreach ($modelProperties as $property) {
            if ($property->getName() === 'code') {
                $valueCode = $property->getValue($model);
            }
        }

        if (!empty($valueCode)) {
            $resource = $this->repository->findOneBy(['code' => StringInflector::nameToCode($valueCode)]);
        }

        if (empty($resource)) {
            $resource = $this->provider->getResource($model);
        }

        return $resource;
    }

}
