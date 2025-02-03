<?php
declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Factory\ProductFactory as ModelProductFactory;
use App\Domain\Factory\ProductCategoryFactory as ModelProductCategoryFactory;
use App\Domain\Service\ProductPriceCalculator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

/**
 * Test product price detail generation
 *  Ensure the product price contains information about the discount, if any.
 */
class ProductDiscoutTest extends KernelTestCase
{
    use Factories;

    protected ProductPriceCalculator $productPriceCalculator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->productPriceCalculator = self::getContainer()->get(ProductPriceCalculator::class);
    }

    /**
     * Tests that the proper discount is applied to the products of a given category
     * @group domain
     * @return void
     */
    public function testCalculateDiscountForCategory(): void
    {
        $this->assertCalculatedDiscountForCategory('boots', 30);
        $this->assertCalculatedDiscountForCategory('sandals', 0);
        $this->assertCalculatedDiscountForCategory('sneakers', 0);
    }

    /**
     * Tests that the proper discount is applied to a specific product
     * @group domain
     * @return void
     */
    public function testCalculateDiscountForProduct(): void
    {
        // this product has two discounts:
        //   - one for the product itself: 15 %
        //   - another for the category it belongs (boots): 30%.
        // It should apply the bigger one (boots): 30%
        $product = ModelProductFactory::createOne([
            'sku'      => '000003',
            'category' => ModelProductCategoryFactory::createOne(['name' => 'boots']),
        ]);
        $priceDetails = $this->productPriceCalculator->getPriceDetails($product);
        $this->assertEquals(30, $priceDetails->discount);

        // this product now will have only one discount: 15%
        $product = ModelProductFactory::createOne([
            'sku'      => '000003',
            'category' => ModelProductCategoryFactory::createOne(['name' => 'sneakers']),
        ]);
        $priceDetails = $this->productPriceCalculator->getPriceDetails($product);
        $this->assertEquals(15, $priceDetails->discount);
    }

    private function assertCalculatedDiscountForCategory(string $category, int $discountPercentage)
    {
        $price      = 10000;
        $finalPrice = $price - $price * $discountPercentage / 100;

        // products from the $category category should have a $discountPercentage % discount
        $product = ModelProductFactory::createOne([
            'category' => ModelProductCategoryFactory::createOne(['name' => $category]),
            'price'    => $price,
        ]);

        $priceDetails = $this->productPriceCalculator->getPriceDetails($product);
        // expect the discount is properly applied
        $this->assertEquals($finalPrice, $priceDetails->final);

        // expect the original price to be 10K
        $this->assertEquals($price, $priceDetails->original);

        // expect the discount to match
        $this->assertEquals($discountPercentage, $priceDetails->discount);
    }
}
