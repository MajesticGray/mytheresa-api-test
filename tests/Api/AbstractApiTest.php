<?php
declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class AbstractApiTest extends ApiTestCase
{
    use ResetDatabase;

    public function setUp(): void
    {
        self::bootKernel([
        ]);
    }

    protected function performApiRequest(string $method, string $url, array $queryParams = [], array $payload = []): ResponseInterface
    {
        return static::createApiClient()
            ->request(method: $method, url: $url, options: [
                'body'  => $payload,
                'query' => $queryParams,
            ]);
    }

    protected function createApiClient(): Client
    {
        return static::createClient([
        ], [
            'headers' => [
                'Accept'       => 'application/vnd.api+json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }
}
