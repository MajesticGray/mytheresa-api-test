<?php
declare(strict_types=1);

namespace App\Domain\Model;

/**
 * Invariable domain structure that holds the details about a product price
 */
class ProductPrice
{
    public function __construct(
        public int $original,
        public int $final,
        public int $discount,
        public string $currency = 'EUR',
    ) {
    }
}
