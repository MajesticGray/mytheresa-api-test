<?php
declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Infrastructure\Doctrine\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'product')]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    public function __construct(
        #[ORM\Column(type: Types::STRING, length: 100, unique: true)]
        #[Assert\NotBlank]
        #[Groups(['product:read'])]
        public string $sku,
        #[ORM\Column(type: Types::STRING, length: 100)]
        #[Assert\NotBlank]
        #[Groups(['product:read'])]
        public string $name,
        #[ORM\ManyToOne(targetEntity: ProductCategory::class, cascade: ['persist'])]
        #[ORM\JoinColumn(nullable: false, onDelete: 'restrict')]
        #[Groups(['product:read'])]
        public ProductCategory $category,
        // The price is an integer; 100.00€ would stored as 10000.
        #[ORM\Column(type: Types::INTEGER)]
        #[Assert\NotBlank]
        #[Groups(['product:read'])]
        public int $price = 0,
    ) {
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    public protected(set) ?int $id = null;
}
