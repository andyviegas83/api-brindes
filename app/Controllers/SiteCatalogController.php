<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Services\SiteCatalogService;

final class SiteCatalogController extends Controller
{
    public function mainCategories(Request $request)
    {
        return $this->success(
            $this->service($request)->mainCategories(),
            'Main categories loaded successfully.'
        );
    }

    public function allCategories(Request $request)
    {
        return $this->success(
            $this->service($request)->allCategories(),
            'Categories loaded successfully.'
        );
    }

    public function products(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $perPage = max((int) $request->query('per_page', 12), 1);
        $category = $request->query('category');
        $search = $request->query('q');

        return $this->success(
            $this->service($request)->products($page, $perPage, is_string($category) ? $category : null, is_string($search) ? $search : null),
            'Products loaded successfully.'
        );
    }

    public function promotions(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $perPage = max((int) $request->query('per_page', 12), 1);

        return $this->success(
            $this->service($request)->promotions($page, $perPage),
            'Promotions loaded successfully.'
        );
    }

    public function launches(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $perPage = max((int) $request->query('per_page', 12), 1);

        return $this->success(
            $this->service($request)->launches($page, $perPage),
            'Launches loaded successfully.'
        );
    }

    public function search(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $perPage = max((int) $request->query('per_page', 12), 1);
        $query = (string) $request->query('q', '');

        return $this->success(
            $this->service($request)->products($page, $perPage, null, $query),
            'Product search completed successfully.'
        );
    }

    public function show(Request $request)
    {
        $identifier = (string) $request->routeParam('idOrSlug', '');
        $product = $this->service($request)->findProduct($identifier);

        if ($product === null) {
            return $this->error('Product not found.', 404, null, 'product_not_found');
        }

        return $this->success($product, 'Product loaded successfully.');
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
