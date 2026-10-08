<?php
declare(strict_types=1);

require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/app/Models/Benchmark.php';
require_once __DIR__ . '/app/Models/Machine.php';
require_once __DIR__ . '/app/Helpers/format.php';
require_once __DIR__ . '/app/Controllers/DashboardController.php';

use Core\Router;

$router = new Router();

require __DIR__ . '/routes/web.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
    '/~oppie/tiny-llm-web'
);
