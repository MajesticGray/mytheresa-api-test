<?php
declare(strict_types=1);

namespace App\Tests\Api;

use App\Infrastructure\Symfony\Factory\ProductFactory;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * Ensures the products are properly filtered by their original price
 */
class ProductPriceFilterTest extends AbstractApiTest
{
    use ResetDatabase, Factories;

    /**
     * Ensures that category filter is applied
     * @group api
     * @return void
     */
    public function testProductPrice(): void
    {
        // create 10 products, prices from 10K up to 100K
        $prices = range(10000, 100000, 10000);
        ProductFactory::createMany(10, fn (int $index) => [
            'price' => $prices[$index - 1],
        ]);

        // Call the API filtering by price and expect the products to match the rule.

        // Filter by price less than or equal to 50K (half of the products)
        $products = $this->requestProductsFilteredByPrice(50000);
        // ensure the API returns only the 5 products
        $this->assertEquals(expected: 5, actual: count($products));
        // ensure that the product prices match the rule
        array_walk($products, fn ($product) => $this->assertLessThanOrEqual(50000, $product['attributes']['price']['original'] ?? null));

        // Filter by price less than or equal to 500K (all products)
        $products = $this->requestProductsFilteredByPrice(500000);
        // ensure the API returns the 10 products
        $this->assertEquals(expected: 10, actual: count($products));
        // ensure that the product prices match the rule
        array_walk($products, fn ($product) => $this->assertLessThanOrEqual(500000, $product['attributes']['price']['original'] ?? null));

        // Filter by price less than or equal to 2K (2 products)
        $products = $this->requestProductsFilteredByPrice(20000);
        // ensure the API returns the 2 products
        $this->assertEquals(expected: 2, actual: count($products));
        // ensure that the product prices match the rule
        array_walk($products, fn ($product) => $this->assertLessThanOrEqual(20000, $product['attributes']['price']['original'] ?? null));
    }

    /**
     * Requests products whose price is equal to or less than $price
     * For the sake of testing, here I bypass the pagination default limit
     *
     * @param int $price
     * @return mixed[]
     */
    private function requestProductsFilteredByPrice(int $price): array
    {
        $response = $this->performApiRequest(
            method: Request::METHOD_GET,
            url: '/products',
            queryParams: [
                'priceLessThan' => $price,
                'itemsPerPage'  => 1000,
            ],
        );

        return $response->toArray(throw: false)['data'] ?? [];
    }
}
