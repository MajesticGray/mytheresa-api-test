<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\DataFixtures;

use App\Infrastructure\Doctrine\Entity\Product;
use App\Infrastructure\Doctrine\Entity\ProductCategory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * This class populates the product table, and it's used only
 *   for development purposes. Should not be used on production.
 */
class ProductFixtures extends AppFixtures implements DependentFixtureInterface
{
    public function getEntities(): iterable
    {
        yield new Product(
            sku: '000001',
            name: 'BV Lean leather ankle boots',
            category: $this->getReference(name: ProductCategoryFixtures::BOOTS, class: ProductCategory::class),
            price: 89000,
        );
        yield new Product(
            sku: '000002',
            name: 'BV Lean leather ankle boots',
            category: $this->getReference(name: ProductCategoryFixtures::BOOTS, class: ProductCategory::class),
            price: 99000,
        );
        yield new Product(
            sku: '000003',
            name: 'Ashlington leather ankle boots',
            category: $this->getReference(name: ProductCategoryFixtures::BOOTS, class: ProductCategory::class),
            price: 71000,
        );
        yield new Product(
            sku: '000004',
            name: 'Naima embellished suede sandals',
            category: $this->getReference(name: ProductCategoryFixtures::SANDALS, class: ProductCategory::class),
            price: 79500,
        );
        yield new Product(
            sku: '000005',
            name: 'Nathane leather sneakers',
            category: $this->getReference(name: ProductCategoryFixtures::SNEAKERS, class: ProductCategory::class),
            price: 59000,
        );
    }

    public function getDependencies(): array
    {
        return [
            ProductCategoryFixtures::class,
        ];
    }
}
