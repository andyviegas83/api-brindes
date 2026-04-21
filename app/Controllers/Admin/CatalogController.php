<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Http\Request;
use App\Services\SiteCatalogService;

final class CatalogController extends Controller
{
    public function categories(Request $request)
    {
        return $this->success(
            $this->service($request)->allCategories(),
            'Admin categories loaded successfully.'
        );
    }

    public function products(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $perPage = max((int) $request->query('per_page', 20), 1);

        return $this->success(
            $this->service($request)->products($page, $perPage),
            'Admin products loaded successfully.'
        );
    }

    public function product(Request $request)
    {
        $identifier = (string) $request->routeParam('idOrSlug', '');
        $product = $this->service($request)->findProduct($identifier);

        if ($product === null) {
            return $this->error('Product not found.', 404, null, 'product_not_found');
        }

        return $this->success($product, 'Admin product loaded successfully.');
    }

    public function update(Request $request)
    {
        return $this->error('Protected write endpoint scaffolded and ready for ERP/CRM integration.', 501, [
            'received_payload' => $request->body(),
        ], 'not_implemented');
    }

    private function service(Request $request): SiteCatalogService
    {
        /** @var \App\Core\Application $app */
        $app = $request->server('app');

        /** @var SiteCatalogService $service */
        $service = $app->container()->get(SiteCatalogService::class);

        return $service;
    }
}
