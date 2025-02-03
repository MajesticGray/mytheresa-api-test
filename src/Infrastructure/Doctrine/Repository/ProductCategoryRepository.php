<?php
declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Infrastructure\Doctrine\Entity\ProductCategory;

/**
 * This repository fetches product categories from the database
 */
class ProductCategoryRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return ProductCategory::class;
    }
}
