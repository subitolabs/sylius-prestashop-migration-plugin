<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\DataTransformer;

use Jgrasp\PrestashopMigrationPlugin\DataTransformer\Model\ModelTransformerInterface;
use Jgrasp\PrestashopMigrationPlugin\DataTransformer\Resource\ResourceTransformerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class PrestashopTransformer implements TransformerInterface
{
    private ModelTransformerInterface $modelTransformer;

    private ResourceTransformerInterface $resourceTransformer;

    public function __construct(ModelTransformerInterface $modelTransformer, ResourceTransformerInterface $resourceTransformer)
    {
        $this->modelTransformer    = $modelTransformer;
        $this->resourceTransformer = $resourceTransformer;
    }

    public function transform($data): array | ResourceInterface | null
    {
        $model = $this->modelTransformer->transform($data);

        return $this->resourceTransformer->transform($model);
    }
}
