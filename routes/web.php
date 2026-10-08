<?php
declare(strict_types=1);

use App\Controllers\MachineController;
use App\Controllers\DashboardController;

$router->get('/', [new DashboardController(), 'index']);
$router->get('/machines', [new DashboardController(), 'machines']);
$router->get('/machines/{id}', [new MachineController(), 'show']);

$router->get('/router-test/{id}', function (string $id): void {
    header('Content-Type: text/plain');
    echo "Dynamic route working. ID: {$id}";
});
