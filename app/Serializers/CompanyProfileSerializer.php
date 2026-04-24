<?php

declare(strict_types=1);

namespace App\Serializers;

use App\Models\CompanyProfile;

final class CompanyProfileSerializer
{
    public function serialize(?CompanyProfile $profile): ?array
    {
        if ($profile === null) {
            return null;
        }

        $payload = $profile->toArray();
        $primaryPhone = $payload['phone']['landline'] ?? $payload['phone']['mobile'] ?? null;

        return $payload + [
            'name' => $payload['trade_name'] ?: ($payload['legal_name'] ?: null),
            'primary_phone' => $primaryPhone,
            'site_logo_url' => $payload['logo_url'] ?? null,
            'site_theme_colors' => $payload['theme_colors'] ?? [],
        ];
    }
}
