<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\StateProvider;

use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Metadata\Operation;
use App\Domain\Service\ProductPriceCalculator;
use App\Domain\Model\Product as ModelProduct;
use App\Domain\Model\ProductPrice as ModelProductPrice;
use App\Domain\Model\ProductCategory as ModelProductCategory;
use App\Application\Dto\ProductDto;
use App\Application\Dto\ProductPriceDto;
use App\Infrastructure\Doctrine\Entity\Product;
use App\Infrastructure\Doctrine\Repository\ProductRepository;
use App\Infrastructure\Symfony\ApiPlatformPaginator;

/**
 * This service fetches the products matching the criteria requested by the API
 * It serves as a middleware between the infrastructure data engine, provided
 *   by Doctrine, and the business rules in the Domain layer.
 *
 * The final results are converted into DTOs suitable for the API.
 */
class ProductDtoProvider implements ProviderInterface
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductPriceCalculator $priceCalculator,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /**
         * Although ApiPlatform is capable of automatically handling pagination,
         *   it does so by integrating directly with Doctrine. In this case, I will not use this mechanism
         *   in order to adhere to the implemented DDD design and properly separate the application layer
         *   from the infrastructure layer.
         */
        $page         = (int)($context['filters']['page'] ?? 1);
        $itemsPerPage = (int)($context['filters']['itemsPerPage'] ?? $operation->getPaginationItemsPerPage() ?? 10);

        /*
         * The same goes with the filters. I will forward them to the repository and handle the logic from there.
         */
        $filters = $context['filters'] ?? [];

        // Fetch paginated Doctrine models (Product)
        $paginatedResult = $this->productRepository->findPaginated($page, $itemsPerPage, $filters);

        // loop products, apply Domain business rules and convert them to DTOs.
        $dtos = array_map(function (Product $product): ProductDto {
            // Convert the doctrine object to to equivalent domain object,
            $modelProduct = $this->convertDoctrineProductToDomainProduct($product);

            // Call the domain business rules, such as price calculation,
            //   using the invariable domain objects
            $price = $this->priceCalculator->getPriceDetails($modelProduct);

            // Then, convert the computed result to a DTO object suitable for the API
            $productDto = $this->convertDomainProductToDto($modelProduct, withPrice: $price);

            return $productDto;
        }, iterator_to_array($paginatedResult->getIterator()));

        // Finally, return the results wrapped into pagination metadata
        return new ApiPlatformPaginator(
            items: $dtos,
            totalItems: count($paginatedResult),
            currentPage: $page,
            itemsPerPage: $itemsPerPage,
        );
    }

    private function convertDoctrineProductToDomainProduct(Product $product): ModelProduct
    {
        return new ModelProduct(
            sku: $product->sku,
            name: $product->name,
            price: $product->price,
            category: new ModelProductCategory(name: $product->category->name),
        );
    }

    private function convertDomainProductToDto(ModelProduct $product, ModelProductPrice $withPrice): ProductDto
    {
        return new ProductDto(
            sku: $product->sku,
            name: $product->name,
            price: new ProductPriceDto(
                original: $withPrice->original,
                final: $withPrice->final,
                discount: $withPrice->discount,
                currency: $withPrice->currency,
            ),
            category: $product->category->name,
        );
    }
}
