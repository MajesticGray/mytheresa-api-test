<?php
declare(strict_types=1);

namespace App\Tests\Api;

use App\Infrastructure\Symfony\Factory\ProductFactory;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * Ensures the response and format of the /products endpoint is valid
 */
class ProductsTest extends AbstractApiTest
{
    use ResetDatabase, Factories;

    /**
     * Ensures the response from the API is valid an contains valid data
     * @group api
     * @return void
     */
    public function testGetProducts(): void
    {
        ProductFactory::createOne();
        $this->performApiRequest(method: Request::METHOD_GET, url: '/products');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame(
            headerName: 'content-type',
            expectedValue: 'application/vnd.api+json; charset=utf-8',
        );

        $this->assertJsonContains([
            'data' => [],
        ]);
    }

    /**
     * Ensures at most 5 items are returned
     * @depends testGetProducts
     * @group api
     * @return void
     */
    public function testMaxProductsReturned(): void
    {
        ProductFactory::createMany(7);

        $response     = $this->performApiRequest(method: Request::METHOD_GET, url: '/products');
        $responseData = $response->toArray(throw: false);

        $this->assertLessThanOrEqual(
            expected: 5,
            actual: count($responseData['data'] ?? []),
        );
    }
}
