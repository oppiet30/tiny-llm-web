<?php
declare(strict_types=1);

use App\Controllers\MachineController;
use App\Controllers\DashboardController;
use App\Controllers\Api\V1\MachineController as ApiMachineController;
use App\Controllers\Api\V1\DatasetController;
use App\Controllers\Api\V1\ModelController;
use App\Controllers\ModelsController;
use App\Controllers\BenchmarkController;
use App\Controllers\Api\V1\BenchmarkController as ApiBenchmarkController;
use App\Controllers\DatasetsController;
use App\Controllers\CompareController;

$router->get('/', [new DashboardController(), 'index']);
$router->get('/compare', [new CompareController(), 'index']);
$router->get('/machines', [new DashboardController(), 'machines']);
$router->get('/machines/{id}', [new MachineController(), 'show']);
$router->get('/api/v1/machines', [new ApiMachineController(), 'index']);
$router->get('/api/v1/machines/{id}', [new ApiMachineController(), 'show']);
$router->get('/api/v1/machines/{id}/runs', [new ApiMachineController(), 'runs']);
$router->get('/api/v1/datasets', [new DatasetController(), 'index']);
$router->get('/datasets', [new DatasetsController(), 'index']);
$router->get('/datasets/{id}', [new DatasetsController(), 'show']);
$router->get('/api/v1/models', [new ModelController(), 'index']);
$router->get('/models', [new ModelsController(), 'index']);
$router->get('/models/{id}', [new ModelsController(), 'show']);
$router->get('/api/v1/datasets/{id}', [new DatasetController(), 'show']);
$router->get('/api/v1/models/{id}', [new ModelController(), 'show']);
$router->get('/api/v1/runs', [new ApiBenchmarkController(), 'index']);
$router->get('/api/v1/runs/{id}', [new ApiBenchmarkController(), 'show']);
$router->get('/runs/{id}', [new BenchmarkController(), 'show']);
