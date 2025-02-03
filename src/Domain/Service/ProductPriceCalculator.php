<?php
declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\Product as ModelProduct;
use App\Domain\Model\ProductPrice as ModelProductPrice;
use App\Domain\Service\DiscountCalculator\DiscountCalculatorInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * This class calculates the final price for a given product
 *  and returns the price details into a ModelProductPrice object
 */
class ProductPriceCalculator
{
    public function __construct(
        #[AutowireIterator('mytheresa.discount.calculator')]
        private iterable $discountCalculators,
        private BestDiscountCalculatorStrategy $strategyResolver,
    ) {
    }

    /**
     * Returns the price details after applying discounts.
     *
     * @param \App\Domain\Model\Product $product
     * @return ModelProductPrice
     */
    public function getPriceDetails(ModelProduct $product): ModelProductPrice
    {
        $discountPercentage = $this->getDiscountPercentage($product);
        $finalPrice         = (int)($product->price - $product->price * $discountPercentage / 100);

        return new ModelProductPrice(
            original: $product->price,
            final: $finalPrice,
            discount: $discountPercentage,
        );
    }

    /**
     * Get discount for a product, using all available calculators
     * @param ModelProduct $product
     * @return int
     */
    private function getDiscountPercentage(ModelProduct $product): int
    {
        $calculators = $this->getDiscountCalculators($product);
        // No discount services available
        if (empty($calculators)) {
            return 0;
        }
        // One discount service available
        if (count($calculators) === 1) {
            $calculator = end($calculators);
        }
        // Multiple Price Discount Calculator services available
        // Determine which one to use using the chosen strategy resolver
        else {
            $calculator = $this->strategyResolver->resolve($product, $calculators);
        }

        return $calculator->getDiscount($product);
    }

    /**
     * Returns the Price Discount Calculator services that this product supports
     * @return DiscountCalculatorInterface[]
     */
    private function getDiscountCalculators(ModelProduct $product): array
    {
        $calculators = iterator_to_array($this->discountCalculators);

        return array_filter($calculators, function ($calculator) use ($product): bool {
            if (!$calculator instanceof DiscountCalculatorInterface) {
                return false;
            }

            return $calculator->supportsProduct($product);
        });
    }
}
