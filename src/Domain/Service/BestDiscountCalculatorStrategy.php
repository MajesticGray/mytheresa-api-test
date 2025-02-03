<?php
declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Service\DiscountCalculator\DiscountCalculatorInterface;
use App\Domain\Model\Product;

/**
 * This service loops all DiscountCalculatorInterface services passed
 *   and returns the one that provides the best discount
 */
class BestDiscountCalculatorStrategy
{
    /**
     * Returns the Price Calculator Service based on "best discount" strategy.
     * @param DiscountCalculatorInterface[] $discountCalculators
     */
    public function resolve(Product $product, iterable $discountCalculators): DiscountCalculatorInterface
    {
        /** @var ?DiscountCalculatorInterface */
        $resolved = null;
        foreach ($discountCalculators as $calculator) {
            if ($resolved === null || $calculator->getDiscount($product) > $resolved->getDiscount($product)) {
                $resolved = $calculator;
            }
        }

        return $resolved;
    }
}
