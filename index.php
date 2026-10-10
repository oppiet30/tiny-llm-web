<?php
declare(strict_types=1);

require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/app/Models/Benchmark.php';
require_once __DIR__ . '/app/Models/Machine.php';
require_once __DIR__ . '/app/Helpers/format.php';
require_once __DIR__ . '/app/Helpers/BenchmarkStatistics.php';
require_once __DIR__ . '/app/Helpers/CompareChartMode.php';
require_once __DIR__ . '/app/Controllers/DashboardController.php';
require_once __DIR__ . '/app/Controllers/MachineController.php';
require_once __DIR__ . '/app/Controllers/ModelsController.php';
require_once __DIR__ . '/app/Controllers/DatasetsController.php';
require_once __DIR__ . '/app/Controllers/Api/V1/MachineController.php';
require_once __DIR__ . '/app/Models/Dataset.php';
require_once __DIR__ . '/app/Controllers/Api/V1/DatasetController.php';
require_once __DIR__ . '/app/Models/Model.php';
require_once __DIR__ . '/app/Controllers/Api/V1/ModelController.php';
require_once __DIR__ . '/app/Controllers/Api/V1/BenchmarkController.php';
require_once __DIR__ . '/app/Controllers/BenchmarkController.php';
require_once __DIR__ . '/app/Controllers/CompareController.php';
require_once __DIR__ . '/app/Controllers/HistoryController.php';

use Core\Router;

$router = new Router();
define('BASE_PATH', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));
require __DIR__ . '/routes/web.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
    BASE_PATH
);
