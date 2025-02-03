<?php
declare(strict_types=1);

namespace App\Application\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * This data transfer object contains information about a Product discount
 * This is the object representation used by the API
 */
class ProductPriceDto
{
    public function __construct(
        #[Groups(['product:read'])]
        public int $original,
        #[Groups(['product:read'])]
        public int $final,
        public int $discount,
        #[Groups(['product:read'])]
        public string $currency,
    ) {
    }

    /**
     * The discount percentage getter. Transforms 0% to null.
     * @var null|string
     */
    #[Groups(['product:read'])]
    #[SerializedName('discount_percentage')]
    public ?string $discountPercentage {
        get => $this->discount === 0 ? null : ($this->discount . '%');
    }
}
