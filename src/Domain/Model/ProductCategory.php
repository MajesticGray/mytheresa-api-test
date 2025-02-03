<?php
declare(strict_types=1);

namespace App\Domain\Model;

/**
 * Invariable domain structure that holds the details about a product category
 */
class ProductCategory
{
    public function __construct(
        public string $name,
    ) {
    }
}
