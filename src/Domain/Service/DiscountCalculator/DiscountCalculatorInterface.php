<?php
declare(strict_types=1);

namespace App\Domain\Service\DiscountCalculator;

use App\Domain\Model\Product;

/**
 * Any classes that deal with product discounts
 *  should implement this interface.
 */
interface DiscountCalculatorInterface
{
    /**
     * Returns the discount applied (percentage)
     * @param Product $product
     * @return int
     */
    public function getDiscount(Product $product): int;

    /**
     * Checks wether this product is candidate for a discount
     * @param Product $product
     * @return bool
     */
    public function supportsProduct(Product $product): bool;
}
