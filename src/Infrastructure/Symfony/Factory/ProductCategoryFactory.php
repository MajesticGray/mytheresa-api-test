<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\Factory;

use App\Infrastructure\Doctrine\Entity\ProductCategory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * This factory creates sample doctrine ProductCategory objects to be used in tests
 */
final class ProductCategoryFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return ProductCategory::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->slug(1),
        ];
    }
}
