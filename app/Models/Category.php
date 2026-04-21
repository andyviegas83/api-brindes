<?php

declare(strict_types=1);

namespace App\Models;

final class Category
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description,
        public readonly ?string $parentId,
        public readonly bool $showInSideMenu,
        public readonly bool $showInTopMenu,
        public readonly ?string $seoTitle,
        public readonly ?string $seoDescription
    ) {
    }

    public static function fromArray(array $row): self
    {
        return new self(
            (string) $row['id'],
            (string) $row['name'],
            (string) $row['slug'],
            $row['description'] ?? null,
            $row['parentId'] ?? null,
            (bool) ($row['showInSideMenu'] ?? false),
            (bool) ($row['showInTopMenu'] ?? false),
            $row['seoTitle'] ?? null,
            $row['seoDescription'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'parent_id' => $this->parentId,
            'show_in_side_menu' => $this->showInSideMenu,
            'show_in_top_menu' => $this->showInTopMenu,
            'seo_title' => $this->seoTitle,
            'seo_description' => $this->seoDescription,
        ];
    }
}
