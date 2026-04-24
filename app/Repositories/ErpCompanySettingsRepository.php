<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Logger;
use App\Models\CompanyProfile;
use Throwable;

final class ErpCompanySettingsRepository extends BaseRepository
{
    public function __construct(
        \App\Database\QueryExecutor $queryExecutor,
        private readonly Logger $logger
    ) {
        parent::__construct($queryExecutor);
    }

    public function findDefault(): ?CompanyProfile
    {
        $table = (string) config('integrations.erp.company_settings_table', 'CompanySettings');

        try {
            $row = $this->queryExecutor->fetchOne(
                $this->erpConnection(),
                sprintf('SELECT * FROM "%s" WHERE "id" = :id LIMIT 1', $table),
                ['id' => 'default']
            );

            if ($row !== null) {
                return CompanyProfile::fromArray($row);
            }
        } catch (Throwable $exception) {
            $this->logger->warning('ERP company settings database lookup failed.', [
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->findDefaultFromPublicApi();
    }

    private function findDefaultFromPublicApi(): ?CompanyProfile
    {
        $baseUrl = rtrim((string) config('app.erp_base_url', ''), '/');

        if ($baseUrl === '') {
            return null;
        }

        $url = $baseUrl . '/company-settings/public';

        for ($attempt = 1; $attempt <= 4; $attempt++) {
            $response = @file_get_contents($url);

            if ($response !== false) {
                $payload = json_decode($response, true);

                if (!is_array($payload) || ($payload['id'] ?? null) === null) {
                    return null;
                }

                return CompanyProfile::fromArray($payload);
            }

            if ($attempt < 4) {
                usleep($attempt * 250000);
            }
        }

        return null;
    }
}
