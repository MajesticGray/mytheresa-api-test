<?php
declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Factory\ProductFactory as ModelProductFactory;
use App\Domain\Model\Product as ModelProduct;
use App\Domain\Service\BestDiscountCalculatorStrategy;
use App\Domain\Service\DiscountCalculator\DiscountCalculatorInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

/**
 * Test the best discount calculator is applied when a collision exists.
 */
class BestDiscountStrategyTest extends KernelTestCase
{
    use Factories;

    protected BestDiscountCalculatorStrategy $strategyResolver;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->strategyResolver = self::getContainer()->get(BestDiscountCalculatorStrategy::class);
    }

    /**
     * Ensure that for any random product with different discounts, the bigger one will be selected
     * @group domain
     * @return void
     */
    public function testBiggerDiscountIsApplied(): void
    {
        $product     = ModelProductFactory::createOne();
        $calculator1 = $this->createDiscountCalculator(30);
        $calculator2 = $this->createDiscountCalculator(40);

        // for any random product with different discounts, the bigger one will be selected
        $resolved = $this->strategyResolver->resolve($product, [$calculator1, $calculator2]);
        $this->assertTrue($calculator2 === $resolved);

        // for any random product with different discounts, the bigger one will be selected
        $calculator1 = $this->createDiscountCalculator(50);
        $calculator2 = $this->createDiscountCalculator(40);

        $resolved = $this->strategyResolver->resolve($product, [$calculator1, $calculator2]);
        $this->assertTrue($calculator1 === $resolved);
    }

    private function createDiscountCalculator(int $discountPercentage): DiscountCalculatorInterface
    {
        return new class($discountPercentage) implements DiscountCalculatorInterface {
            public function __construct(private int $discount)
            {
            }

            public function getDiscount(ModelProduct $product): int
            {
                return $this->discount;
            }

            public function supportsProduct(ModelProduct $product): bool
            {
                return true;
            }
        };
    }
}
