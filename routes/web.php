<?php
declare(strict_types=1);

use App\Controllers\DashboardController;

$router->get('/', [new DashboardController(), 'index']);
$router->get('/machines', [new DashboardController(), 'machines']);

$router->get('/mvc-test', [new DashboardController(), 'index']);
