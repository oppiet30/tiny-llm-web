<?php
declare(strict_types=1);

namespace App\Controllers\Api\V1;

use Core\ApiResponse;

use App\Models\Benchmark;

class BenchmarkController
{
    public function index(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        require __DIR__ . '/../../../../config.php';

        $model = new Benchmark($db);
        $runs = $model->all();

        echo json_encode(
            [
                'data' => $runs,
                'count' => count($runs)
            ],
            JSON_PRETTY_PRINT
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
        );
    }
    public function show(string $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!ApiResponse::validId($id)) {
            http_response_code(404);
            echo json_encode(['error' => 'Benchmark run not found']);
            return;
        }

        require __DIR__ . '/../../../../config.php';

        $model = new Benchmark($db);
        $run = $model->find((int)$id);

        if ($run === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Benchmark run not found']);
            return;
        }

        echo json_encode(
            ['data' => $run],
            JSON_PRETTY_PRINT
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
        );
    }
}
