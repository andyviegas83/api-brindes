<?php

declare(strict_types=1);

use App\Config\Config;
use App\Config\Env;
use App\Core\Application;
use App\Core\Container;
use App\Core\ErrorHandler;
use App\Core\Logger;
use App\Database\ConnectionFactory;
use App\Database\DatabaseManager;
use App\Database\QueryExecutor;
use App\Repositories\ContentAdapterRepository;
use App\Repositories\ErpCategoryRepository;
use App\Repositories\ErpCompanySettingsRepository;
use App\Repositories\ErpProductRepository;
use App\Serializers\CategorySerializer;
use App\Serializers\CompanyProfileSerializer;
use App\Serializers\PaginationSerializer;
use App\Serializers\ProductSerializer;
use App\Services\SiteCatalogService;
use App\Services\SiteContentService;

$autoloadFile = base_path('vendor/autoload.php');

if (is_file($autoloadFile)) {
    require_once $autoloadFile;
} else {
    require_once __DIR__ . '/autoload.php';
}

Env::load(base_path('.env'));
Config::load(base_path('app/Config'));

date_default_timezone_set((string) config('app.timezone', 'UTC'));

$logger = new Logger(
    (string) config('app.log_path', base_path('logs/app.log')),
    (string) config('app.log_level', 'debug')
);

ErrorHandler::register($logger, (bool) config('app.debug', false));

$container = new Container();
$container->set(Logger::class, $logger);
$container->singleton(ConnectionFactory::class, static fn (): ConnectionFactory => new ConnectionFactory());
$container->singleton(DatabaseManager::class, static fn (Container $container): DatabaseManager => new DatabaseManager(
    (array) config('database'),
    $container->get(ConnectionFactory::class)
));
$container->singleton(QueryExecutor::class, static fn (Container $container): QueryExecutor => new QueryExecutor(
    $container->get(DatabaseManager::class)
));
$container->singleton(ErpCompanySettingsRepository::class, static fn (Container $container): ErpCompanySettingsRepository => new ErpCompanySettingsRepository(
    $container->get(QueryExecutor::class)
));
$container->singleton(ErpCategoryRepository::class, static fn (Container $container): ErpCategoryRepository => new ErpCategoryRepository(
    $container->get(QueryExecutor::class)
));
$container->singleton(ErpProductRepository::class, static fn (Container $container): ErpProductRepository => new ErpProductRepository(
    $container->get(QueryExecutor::class)
));
$container->singleton(ContentAdapterRepository::class, static fn (Container $container): ContentAdapterRepository => new ContentAdapterRepository(
    $container->get(QueryExecutor::class),
    $container->get(Logger::class)
));
$container->singleton(CompanyProfileSerializer::class, static fn (): CompanyProfileSerializer => new CompanyProfileSerializer());
$container->singleton(CategorySerializer::class, static fn (): CategorySerializer => new CategorySerializer());
$container->singleton(ProductSerializer::class, static fn (): ProductSerializer => new ProductSerializer());
$container->singleton(PaginationSerializer::class, static fn (): PaginationSerializer => new PaginationSerializer());
$container->singleton(SiteCatalogService::class, static fn (Container $container): SiteCatalogService => new SiteCatalogService(
    $container->get(ErpCategoryRepository::class),
    $container->get(ErpProductRepository::class),
    $container->get(ContentAdapterRepository::class),
    $container->get(CategorySerializer::class),
    $container->get(ProductSerializer::class),
    $container->get(PaginationSerializer::class),
    $container->get(Logger::class)
));
$container->singleton(SiteContentService::class, static fn (Container $container): SiteContentService => new SiteContentService(
    $container->get(ErpCompanySettingsRepository::class),
    $container->get(SiteCatalogService::class),
    $container->get(ContentAdapterRepository::class),
    $container->get(CompanyProfileSerializer::class),
    $container->get(Logger::class)
));

return new Application($logger, $container);
