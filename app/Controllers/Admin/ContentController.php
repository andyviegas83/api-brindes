<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Http\Request;
use App\Services\SiteContentService;

final class ContentController extends Controller
{
    public function banners(Request $request)
    {
        return $this->success(
            $this->service($request)->homepage()['main_banners'],
            'Banners loaded successfully.'
        );
    }

    public function siteTexts(Request $request)
    {
        return $this->success(
            $this->service($request)->siteSettings()['texts'],
            'Site texts loaded successfully.'
        );
    }

    public function update(Request $request)
    {
        return $this->error('Protected write endpoint scaffolded and ready for ERP/CRM integration.', 501, [
            'received_payload' => $request->body(),
        ], 'not_implemented');
    }

    private function service(Request $request): SiteContentService
    {
        /** @var \App\Core\Application $app */
        $app = $request->server('app');

        /** @var SiteContentService $service */
        $service = $app->container()->get(SiteContentService::class);

        return $service;
    }
}
