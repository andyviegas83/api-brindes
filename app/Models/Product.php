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
        public readonly ?string $categoryName,
        public readonly ?string $inclusionDate,
        public readonly array $images,
        public readonly array $categoryAssignments,
        public readonly bool $isLaunch,
        public readonly bool $isPromotion
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
            $row['categoryName'] ?? null,
            isset($row['inclusionDate']) ? (string) $row['inclusionDate'] : null,
            self::decodeArray($row['images'] ?? []),
            self::decodeArray($row['categoryAssignments'] ?? []),
            (bool) ($row['isLaunch'] ?? false),
            (bool) ($row['isPromotion'] ?? false)
        );
    }

    private static function decodeArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
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
            'inclusion_date' => $this->inclusionDate,
            'images' => $this->images,
            'category_assignments' => $this->categoryAssignments,
            'is_launch' => $this->isLaunch,
            'is_promotion' => $this->isPromotion,
            'category' => [
                'id' => $this->categoryId,
                'name' => $this->categoryName,
            ],
        ];
    }
}
