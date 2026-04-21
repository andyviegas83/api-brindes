<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CompanyProfile;

final class ErpCompanySettingsRepository extends BaseRepository
{
    public function findDefault(): ?CompanyProfile
    {
        $table = (string) config('integrations.erp.company_settings_table', 'CompanySettings');

        $row = $this->queryExecutor->fetchOne(
            $this->erpConnection(),
            sprintf('SELECT * FROM "%s" WHERE "id" = :id LIMIT 1', $table),
            ['id' => 'default']
        );

        return $row === null ? null : CompanyProfile::fromArray($row);
    }
}
