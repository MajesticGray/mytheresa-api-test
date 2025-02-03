<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\DataFixtures;

use App\Infrastructure\Doctrine\Entity\ProductCategory;

/**
 * This class populates the product_category table, and it's used only
 *   for development purposes. Should not be used on production.
 */
class ProductCategoryFixtures extends AppFixtures
{
    public const BOOTS    = 'BOOTS';
    public const SANDALS  = 'SANDALS';
    public const SNEAKERS = 'SNEAKERS';

    public function getEntities(): iterable
    {
        $category = new ProductCategory(name: 'boots');
        $this->addReference(self::BOOTS, $category);
        yield $category;

        $category = new ProductCategory(name: 'sandals');
        $this->addReference(self::SANDALS, object: $category);
        yield $category;

        $category = new ProductCategory(name: 'sneakers');
        $this->addReference(self::SNEAKERS, object: $category);
        yield $category;
    }
}
