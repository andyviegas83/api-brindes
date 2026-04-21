<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;

final class HealthController extends Controller
{
    public function __invoke(Request $request)
    {
        return $this->check($request);
    }

    public function check(Request $request)
    {
        return $this->success([
            'service' => config('app.name'),
            'environment' => config('app.env'),
            'site_base_url' => config('app.site_base_url'),
            'erp_base_url' => config('app.erp_base_url'),
        ], 'API is running.');
    }
}
