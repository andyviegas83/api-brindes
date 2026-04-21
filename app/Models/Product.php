<?php

declare(strict_types=1);

namespace App\Models;

final class Product
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $sku,
        public readonly string $type,
        public readonly string $status,
        public readonly ?string $shortDescription,
        public readonly ?string $technicalDetails,
        public readonly ?int $minimumQuantity,
        public readonly ?int $leadTimeDays,
        public readonly string $categoryId,
        public readonly ?string $categoryName
    ) {
    }

    public static function fromArray(array $row): self
    {
        return new self(
            (string) $row['id'],
            (string) $row['name'],
            (string) $row['slug'],
            $row['sku'] ?? null,
            (string) $row['type'],
            (string) $row['status'],
            $row['shortDescription'] ?? null,
            $row['technicalDetails'] ?? null,
            isset($row['minimumQuantity']) ? (int) $row['minimumQuantity'] : null,
            isset($row['leadTimeDays']) ? (int) $row['leadTimeDays'] : null,
            (string) $row['categoryId'],
            $row['categoryName'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'type' => $this->type,
            'status' => $this->status,
            'short_description' => $this->shortDescription,
            'technical_details' => $this->technicalDetails,
            'minimum_quantity' => $this->minimumQuantity,
            'lead_time_days' => $this->leadTimeDays,
            'category' => [
                'id' => $this->categoryId,
                'name' => $this->categoryName,
            ],
        ];
    }
}
