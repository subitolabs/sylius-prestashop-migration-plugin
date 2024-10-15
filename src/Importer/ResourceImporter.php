<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\Importer;

use Doctrine\ORM\EntityManagerInterface;
use Jgrasp\PrestashopMigrationPlugin\DataCollector\DataCollectorInterface;
use Jgrasp\PrestashopMigrationPlugin\Persister\PersisterInterface;
use Jgrasp\PrestashopMigrationPlugin\Validator\ViolationBagInterface;
use JMS\Serializer\SerializerInterface;

class ResourceImporter implements ImporterInterface
{
    private string $name;

    private int $step;

    private DataCollectorInterface $collector;

    private PersisterInterface $persister;

    private EntityManagerInterface $entityManager;

    private ViolationBagInterface $violationBag;

    private SerializerInterface $serializer;

    public function __construct(
        string $name,
        int $step,
        DataCollectorInterface $collector,
        PersisterInterface $persister,
        EntityManagerInterface $entityManager,
        ViolationBagInterface $violationBag,
        SerializerInterface $serializer
    ) {
        $this->name          = $name;
        $this->step          = $step;
        $this->collector     = $collector;
        $this->persister     = $persister;
        $this->entityManager = $entityManager;
        $this->violationBag  = $violationBag;
        $this->serializer    = $serializer;
    }

    public function import(array $criteria = [], callable $callable = null): void
    {
        for ($offset = 0, $size = $this->size(); $offset < $size; $offset += $this->step) {
            $collection = $this->collector->collect($criteria, $this->step, $offset);

            if (empty($collection)) {
                return ;
            }

            foreach ($collection as $item) {
                $this->persister->persist($item);
            }

            $this->entityManager->flush();

            if (null !== $callable) {
                $callable($this->step, $this->violationBag->all());
            }
        }
    }

    public function size(array $criteria = [], ?int $limit = null): int
    {
        $s = $this->collector->size($criteria);

        if (!empty($limit) && $limit < $s) {
            return $limit;
        }

        return $s;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
