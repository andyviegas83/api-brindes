<?php

declare(strict_types=1);

namespace App\Serializers;

use App\Models\Category;

final class CategorySerializer
{
    public function serialize(Category $category): array
    {
        return $category->toArray();
    }

    public function collection(array $categories): array
    {
        return array_map(fn (Category $category): array => $this->serialize($category), $categories);
    }
}
