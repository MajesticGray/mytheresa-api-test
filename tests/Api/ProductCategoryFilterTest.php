<?php
declare(strict_types=1);

namespace App\Tests\Api;

use App\Infrastructure\Symfony\Factory\ProductCategoryFactory;
use App\Infrastructure\Symfony\Factory\ProductFactory;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * Ensures the products are properly filtered by category
 */
class ProductCategoryFilterTest extends AbstractApiTest
{
    use ResetDatabase, Factories;

    /**
     * Ensures that category filter is applied
     * @group api
     * @return void
     */
    public function testProductCategory(): void
    {
        $categories = [
            'boots'    => ProductCategoryFactory::createOne(['name' => 'boots']),
            'sandals'  => ProductCategoryFactory::createOne(['name' => 'sandals']),
            'sneakers' => ProductCategoryFactory::createOne(['name' => 'sneakers']),
        ];
        // create 5 products from the 3 previous categories
        ProductFactory::createMany(5, function (int $index) use ($categories) {
            return [
                'category' => match ($index) {
                    1, 2, 3 => $categories['boots'],
                    4       => $categories['sandals'],
                    default => $categories['sneakers'],
                },
            ];
        });

        // Call the API filtering by category and expect the products to match the rule.
        // Filter by boots category
        $products = $this->requestProductsFilteredByCategory('boots');
        // ensure the API returns only the 3 products
        $this->assertEquals(expected: 3, actual: count($products));
        // ensure the products returned belong to the 'boots' category
        array_walk($products, fn ($product) => $this->assertEquals('boots', $product['attributes']['category'] ?? null));

        // Filter by sandals category
        $products = $this->requestProductsFilteredByCategory('sandals');
        // ensure the API returns only the 1 product
        $this->assertEquals(expected: 1, actual: count($products));
        // ensure the products returned belong to the 'sandals' category
        array_walk($products, fn ($product) => $this->assertEquals('sandals', $product['attributes']['category'] ?? null));

        // Filter by sneakers category
        $products = $products = $this->requestProductsFilteredByCategory('sneakers');
        // ensure the API returns only the 1 product
        $this->assertEquals(expected: 1, actual: count($products));
        // ensure the products returned belong to the 'sneakers' category
        array_walk($products, fn ($product) => $this->assertEquals('sneakers', $product['attributes']['category'] ?? null));
    }

    /**
     * Requests products from the specified category and returns the API json
     * @param string $category
     * @group api
     * @return mixed[]
     */
    private function requestProductsFilteredByCategory(string $category): array
    {
        $response = $this->performApiRequest(
            method: Request::METHOD_GET,
            url: '/products',
            queryParams: [
                'category' => $category,
            ],
        );

        return $response->toArray(throw: false)['data'] ?? [];
    }
}
