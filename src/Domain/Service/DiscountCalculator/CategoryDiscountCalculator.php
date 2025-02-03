<?php
declare(strict_types=1);

namespace App\Domain\Service\DiscountCalculator;

use App\Domain\Model\Product;

/**
 * Calculates the discount applied to a product based on its category
 */
class CategoryDiscountCalculator implements DiscountCalculatorInterface
{
    public function getDiscount(Product $product): int
    {
        // map a category with its discount
        return match ($product->category) {
            // ther's only one category supported, and so the discount is fixed.
            default => 30,
        };
    }

    public function supportsProduct(Product $product): bool
    {
        // return `true` to support all categories
        return $product->category->name === 'boots';
    }
}
