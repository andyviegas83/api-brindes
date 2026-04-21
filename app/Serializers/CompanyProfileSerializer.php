<?php

declare(strict_types=1);

namespace App\Serializers;

use App\Models\CompanyProfile;

final class CompanyProfileSerializer
{
    public function serialize(?CompanyProfile $profile): ?array
    {
        return $profile?->toArray();
    }
}
