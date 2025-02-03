<?php
declare(strict_types=1);

namespace App\Application\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Infrastructure\Symfony\StateProvider\ProductDtoProvider;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * This data transfer object contains information about a Product
 * This is the object representation used by the API
 */
#[ApiResource(
    operations: [
        /**
         */
        new GetCollection(
            uriTemplate: '/products',
            name: 'get_products',
            paginationEnabled: true,
            paginationItemsPerPage: 5,
            paginationClientItemsPerPage: true,
            paginationMaximumItemsPerPage: 100,
            normalizationContext: ['groups' => ['product:read'], 'skip_null_values' => false],
            order: ['id' => 'ASC'],
            provider: ProductDtoProvider::class,
        ),
    ],
)]
class ProductDto
{
    public function __construct(
        #[Groups(['product:read'])]
        public string $sku,
        #[Groups(['product:read'])]
        public string $name,
        #[Groups(['product:read'])]
        public string $category,
        #[Groups(['product:read'])]
        public ProductPriceDto $price,
    ) {
    }
}
