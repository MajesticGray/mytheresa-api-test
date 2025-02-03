<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\Factory;

use App\Infrastructure\Doctrine\Entity\Product;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * This factory creates sample doctrine Product objects to be used in tests
 */
final class ProductFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Product::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'category' => ProductCategoryFactory::new(),
            'name'     => self::faker()->text(50),
            'price'    => self::faker()->numberBetween(10000, 100000),
            'sku'      => $this->generateSku(),
        ];
    }

    private function generateSku(): string
    {
        return str_pad((string)self::faker()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}
