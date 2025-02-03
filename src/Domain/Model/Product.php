<?php
declare(strict_types=1);

namespace App\Domain\Model;

/**
 * Invariable domain structure that holds the details about a product
 */
class Product
{
    public function __construct(
        public readonly string $sku,
        public string $name,
        public int $price,
        public ProductCategory $category,
    ) {
    }
}
