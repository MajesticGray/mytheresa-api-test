<?php
declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Model\ProductCategory as ModelProductCategory;
use Zenstruck\Foundry\Factory;

/**
 * This factory creates sample domain ProductCategory objects to be used in tests
 */
final class ProductCategoryFactory extends Factory
{
    public function create(array|callable $attributes = []): mixed
    {
        if (is_callable($attributes)) {
            $attributes = $attributes();
        }
        $attributes = array_merge($this->defaults(), $attributes);

        return new ModelProductCategory(
            name: $attributes['name'],
        );
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->slug(1),
        ];
    }

    protected static function getClass(): string
    {
        return ModelProductCategory::class;
    }
}
