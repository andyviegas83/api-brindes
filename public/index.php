<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap/helpers.php';

/** @var \App\Core\Application $app */
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->bootstrapRoutes(base_path('routes/api.php'));

$request = \App\Http\Request::capture()->withServerValue('app', $app);
$response = $app->handle($request);
$response->send();
