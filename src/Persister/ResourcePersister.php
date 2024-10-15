<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\Persister;

use Doctrine\ORM\EntityManagerInterface;
use Jgrasp\PrestashopMigrationPlugin\DataTransformer\TransformerInterface;
use Jgrasp\PrestashopMigrationPlugin\Validator\ValidatorInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class ResourcePersister implements PersisterInterface
{
    private EntityManagerInterface $manager;

    private TransformerInterface $transformer;

    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $manager, TransformerInterface $transformer, ValidatorInterface $validator)
    {
        $this->manager     = $manager;
        $this->transformer = $transformer;
        $this->validator   = $validator;
    }

    public function persist(array $data): void
    {
        $resource = $this->transformer->transform($data);

        if (empty($resource)) {
            return ;
        }

        $resources = is_array($resource) ? $resource : [$resource];

        foreach ($resources as $resource) {
            if ($this->validator->validate($resource)) {
                $this->manager->persist($resource);
            }
        }
    }
}
