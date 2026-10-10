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

$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
if (BASE_PATH !== '' && ($requestPath === BASE_PATH || str_starts_with($requestPath, BASE_PATH . '/'))) {
    $requestPath = substr($requestPath, strlen(BASE_PATH));
}
$isApi = \Core\ApiResponse::isApiPath('/' . trim($requestPath, '/'));

if ($isApi) {
    // Keep PHP warnings and connection details out of JSON responses.
    ob_start();
    set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        throw new \ErrorException($message, 0, $severity, $file, $line);
    });
}

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], BASE_PATH);
    if ($isApi) {
        ob_end_flush();
    }
} catch (\Throwable $error) {
    if (!$isApi) {
        throw $error;
    }
    ob_end_clean();
    error_log((string) $error);
    \Core\ApiResponse::error(500, 'Internal server error');
} finally {
    if ($isApi) {
        restore_error_handler();
    }
}
