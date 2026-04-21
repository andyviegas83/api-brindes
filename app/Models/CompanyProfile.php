<?php

declare(strict_types=1);

namespace App\Models;

final class CompanyProfile
{
    public function __construct(
        public readonly ?string $legalName,
        public readonly ?string $tradeName,
        public readonly ?string $document,
        public readonly ?string $email,
        public readonly ?string $landlinePhone,
        public readonly ?string $mobilePhone,
        public readonly ?string $logoUrl,
        public readonly ?string $faviconUrl,
        public readonly array $socialLinks,
        public readonly array $address
    ) {
    }

    public static function fromArray(array $row): self
    {
        return new self(
            $row['legalName'] ?? null,
            $row['tradeName'] ?? null,
            $row['document'] ?? null,
            $row['email'] ?? null,
            $row['landlinePhone'] ?? null,
            $row['mobilePhone'] ?? null,
            $row['logoUrl'] ?? null,
            $row['faviconUrl'] ?? null,
            [
                'google' => $row['googleUrl'] ?? null,
                'reclame_aqui' => $row['reclameAquiUrl'] ?? null,
                'instagram' => $row['instagramUrl'] ?? null,
                'facebook' => $row['facebookUrl'] ?? null,
                'linkedin' => $row['linkedinUrl'] ?? null,
                'tiktok' => $row['tiktokUrl'] ?? null,
                'youtube' => $row['youtubeUrl'] ?? null,
                'pinterest' => $row['pinterestUrl'] ?? null,
            ],
            [
                'street' => $row['street'] ?? null,
                'number' => $row['number'] ?? null,
                'complement' => $row['complement'] ?? null,
                'district' => $row['district'] ?? null,
                'state' => $row['state'] ?? null,
                'city' => $row['city'] ?? null,
                'country' => $row['country'] ?? null,
                'zip_code' => $row['zipCode'] ?? null,
            ]
        );
    }

    public function toArray(): array
    {
        return [
            'legal_name' => $this->legalName,
            'trade_name' => $this->tradeName,
            'document' => $this->document,
            'email' => $this->email,
            'phone' => [
                'landline' => $this->landlinePhone,
                'mobile' => $this->mobilePhone,
            ],
            'logo_url' => $this->logoUrl,
            'favicon_url' => $this->faviconUrl,
            'social_links' => $this->socialLinks,
            'address' => $this->address,
        ];
    }
}
