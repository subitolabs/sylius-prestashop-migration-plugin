<?php

declare(strict_types=1);

namespace Jgrasp\PrestashopMigrationPlugin\Repository\Product;

use Jgrasp\PrestashopMigrationPlugin\Repository\EntityRepository;

class ProductFeatureRepository extends EntityRepository
{
    /**
     * Retrieve product feature values
     */
    public function getFeatureValues(int $featureId): array
    {
        $query = $this->createQueryBuilder();

        $query
            ->select($this->getValueLangTable() . '.*')
            ->from($this->getValueTable())
            ->join(
                $this->getValueTable(),
                $this->getValueLangTable(),
                $this->getValueLangTable(),
                $query->expr()->comparison($this->getValueTable() . '.id_feature_value', '=', $this->getValueLangTable() . '.id_feature_value')
            )
            ->where($query->expr()->eq($this->getValueTable() . '.id_feature', $featureId));

        return $this->getConnection()->executeQuery($query->getSQL())->fetchAllAssociative();
    }

    private function getValueTable()
    {
        return $this->getTable() . '_value';
    }

    private function getValueLangTable()
    {
        return $this->getTable() . '_value_lang';
    }
}
