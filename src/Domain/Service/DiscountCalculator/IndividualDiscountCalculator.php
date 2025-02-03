<?php
declare(strict_types=1);

namespace App\Domain\Service\DiscountCalculator;

use App\Domain\Model\Product;

/**
 * Calculates the discount applied to an specific product based on its sku
 */
class IndividualDiscountCalculator implements DiscountCalculatorInterface
{
    public function getDiscount(Product $product): int
    {
        // As only one product is candidate for a discount,
        //  the discount is fixed
        return 15;
    }

    public function supportsProduct(Product $product): bool
    {
        // We will apply the discount logic only on this product
        return $product->sku === '000003';
    }
}
