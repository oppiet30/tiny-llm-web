<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Model;
use App\Models\Benchmark;

class ModelsController
{
    public function index(): void
    {
        require __DIR__ . '/../../config.php';

        $model = new Model($db);
        $models = $model->all();

        require __DIR__ . '/../Views/models.php';
    }
    public function show(string $id): void
    {
        if (!ctype_digit($id) || (int)$id < 1) {
            http_response_code(404);
            echo '404 - Model Not Found';
            return;
        }

        require __DIR__ . '/../../config.php';

        $modelRepository = new Model($db);
        $model = $modelRepository->find((int)$id);

    if ($model === null) {
        http_response_code(404);
        echo '404 - Model Not Found';
        return;
    }

    $benchmarkRepository = new Benchmark($db);
    $benchmarks = $benchmarkRepository->forModel((int)$id);

    require __DIR__ . '/../Views/model.php';

   }
}
