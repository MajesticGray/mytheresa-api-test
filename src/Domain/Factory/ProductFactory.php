<?php
declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Model\Product as ModelProduct;
use Zenstruck\Foundry\Factory;

/**
 * This factory creates sample domain Product objects to be used in tests
 */
final class ProductFactory extends Factory
{
    public function create(array|callable $attributes = []): mixed
    {
        if (is_callable($attributes)) {
            $attributes = $attributes();
        }
        $attributes = array_merge($this->defaults(), $attributes);

        return new ModelProduct(
            sku: $attributes['sku'],
            name: $attributes['name'],
            price: $attributes['price'],
            category: $attributes['category'],
        );
    }

    protected function defaults(): array|callable
    {
        return [
            'sku'      => $this->generateSku(),
            'name'     => implode(' ', self::faker()->words(rand(2, 6))),
            'price'    => self::faker()->numberBetween(10000, 100000),
            'category' => ProductCategoryFactory::createOne(),
        ];
    }

    protected static function getClass(): string
    {
        return ModelProduct::class;
    }

    private function generateSku(): string
    {
        return str_pad((string)self::faker()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}
