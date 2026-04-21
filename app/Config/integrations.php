<?php

declare(strict_types=1);

use App\Config\Env;

return [
    'erp' => [
        'connection' => config('database.erp_connection', 'erp'),
        'company_settings_table' => 'CompanySettings',
        'categories_table' => 'Category',
        'products_table' => 'Product',
        'banners_sql' => Env::get('ERP_CONTENT_BANNERS_SQL', ''),
        'testimonials_sql' => Env::get('ERP_CONTENT_TESTIMONIALS_SQL', ''),
        'promotions_sql' => Env::get('ERP_CONTENT_PROMOTIONS_SQL', ''),
        'releases_sql' => Env::get('ERP_CONTENT_RELEASES_SQL', ''),
        'institutional_texts_sql' => Env::get('ERP_CONTENT_INSTITUTIONAL_TEXTS_SQL', ''),
    ],
];
