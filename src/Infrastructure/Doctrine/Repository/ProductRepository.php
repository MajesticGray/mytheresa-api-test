<?php
declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Model\Product as ModelProduct;
use App\Domain\Model\ProductCategory;
use App\Infrastructure\Doctrine\Entity\Product;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * This repository fetches products from the database
 * It contains some utility methods
 */
class ProductRepository extends AbstractRepository
{
    public function findPaginated(int $page, int $itemsPerPage, array $filters = []): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage);

        // Filter by Category Name
        if (!empty($filters['category'])) {
            $queryBuilder
                ->join('p.category', 'c')
                ->andWhere('c.name = :categoryName')
                ->setParameter('categoryName', $filters['category']);
        }

        if (!empty($filters['priceLessThan'])) {
            $queryBuilder
                ->andWhere('p.price <= :priceLessThan')
                ->setParameter('priceLessThan', (int)$filters['priceLessThan']);
        }

        return new Paginator($queryBuilder);
    }

    public function findBySku(string $sku): ?ModelProduct
    {
        $model = $this->findOneBy([
            'sku' => $sku,
        ]);

        return $model === null ? null : $this->convertToDomain($model);
    }

    public function findByCategory(ProductCategory $category): array
    {
        return $this->findBy([
            'category' => $category,
        ]);
    }

    protected function getEntityClass(): string
    {
        return Product::class;
    }
}
