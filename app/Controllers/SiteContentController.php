<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Services\SiteContentService;

final class SiteContentController extends Controller
{
    public function settings(Request $request)
    {
        return $this->success(
            $this->service($request)->siteSettings(),
            'Site settings loaded successfully.'
        );
    }

    public function navigation(Request $request)
    {
        return $this->success(
            $this->service($request)->navigation(),
            'Navigation loaded successfully.'
        );
    }

    public function homepage(Request $request)
    {
        return $this->success(
            $this->service($request)->homepage(),
            'Homepage content loaded successfully.'
        );
    }

    public function about(Request $request)
    {
        return $this->success(
            $this->service($request)->page('about'),
            'About page loaded successfully.'
        );
    }

    public function faq(Request $request)
    {
        return $this->success(
            $this->service($request)->page('faq'),
            'FAQ page loaded successfully.'
        );
    }

    public function contact(Request $request)
    {
        return $this->success(
            $this->service($request)->page('contact'),
            'Contact page loaded successfully.'
        );
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
