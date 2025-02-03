<?php
declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Infrastructure\Doctrine\Repository\ProductCategoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name: 'product_category')]
#[ORM\Entity(repositoryClass: ProductCategoryRepository::class)]
class ProductCategory
{
    public function __construct(
        #[ORM\Column(type: Types::STRING, length: 100)]
        #[Groups(['product:read'])]
        public string $name,
    ) {
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    public protected(set) ?int $id = null;
}
