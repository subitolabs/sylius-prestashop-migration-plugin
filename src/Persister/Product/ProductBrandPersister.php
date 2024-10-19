<?php

namespace Jgrasp\PrestashopMigrationPlugin\Persister\Product;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Jgrasp\PrestashopMigrationPlugin\Entity\PrestashopTrait;
use Jgrasp\PrestashopMigrationPlugin\Persister\PersisterInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;

class ProductBrandPersister implements PersisterInterface
{
    private array $cacheBrandByPrestashopID = [];
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ObjectRepository $brandRepository,
    )
    {
        /** @var PrestashopTrait $brand */
        foreach($this->brandRepository->findAll() as $brand) {
            $this->cacheBrandByPrestashopID[$brand->getPrestashopId()] = $brand;
        }
    }

    public function persist(array $data): void
    {
        if (!empty($data['id_manufacturer'])) {
            $brand = $this->cacheBrandByPrestashopID[$data['id_manufacturer']];
            $product = $this->productRepository->findOneBy(['prestashopId' => $data['id_product']]);

            if (!empty($product)) {
                $product->setBrand($brand);
            }
        }
    }
}
