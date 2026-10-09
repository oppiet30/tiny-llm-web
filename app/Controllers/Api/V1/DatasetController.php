<?php
declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Models\Dataset;

class DatasetController
{
    public function index(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        require __DIR__ . '/../../../../config.php';

        $model = new Dataset($db);
        $datasets = $model->all();

        echo json_encode(
            [
                'data' => $datasets,
                'count' => count($datasets)
            ],
            JSON_PRETTY_PRINT
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
        );
    }
    public function show(string $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!ctype_digit($id) || (int)$id < 1) {
            http_response_code(404);
            echo json_encode(['error' => 'Dataset not found']);
            return;
        }

        require __DIR__ . '/../../../../config.php';

        $model = new Dataset($db);
        $dataset = $model->find((int)$id);

        if ($dataset === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Dataset not found']);
            return;
        }

        echo json_encode(
            ['data' => $dataset],
            JSON_PRETTY_PRINT
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
        );
    }
}
