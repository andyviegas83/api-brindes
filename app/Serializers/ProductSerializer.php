<?php

declare(strict_types=1);

namespace App\Serializers;

use App\Models\Product;

final class ProductSerializer
{
    public function serialize(Product $product): array
    {
        return $product->toArray();
    }

    public function collection(array $products): array
    {
        return array_map(fn (Product $product): array => $this->serialize($product), $products);
    }
}
