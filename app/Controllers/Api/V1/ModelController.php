<?php
declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Models\Model;

class ModelController
{
    public function index(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        require __DIR__ . '/../../../../config.php';

        $model = new Model($db);
        $models = $model->all();

        echo json_encode(
            [
                'data' => $models,
                'count' => count($models)
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
            echo json_encode(['error' => 'Model not found']);
            return;
        }

        require __DIR__ . '/../../../../config.php';

        $model = new Model($db);
        $record = $model->find((int)$id);

        if ($record === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Model not found']);
            return;
        }

        echo json_encode(
            ['data' => $record],
            JSON_PRETTY_PRINT
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
        );
    }
}
