<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Dataset;
use App\Models\Benchmark;

class DatasetsController
{
    public function index(): void
    {
        require __DIR__ . '/../../config.php';

        $datasetRepository = new Dataset($db);
        $datasets = $datasetRepository->all();

        require __DIR__ . '/../Views/datasets.php';
    }
    public function show(string $id): void
    {
        if (!ctype_digit($id) || (int)$id < 1) {
            http_response_code(404);
            echo '404 - Dataset Not Found';
            return;
        }

        require __DIR__ . '/../../config.php';

        $datasetRepository = new Dataset($db);
        $dataset = $datasetRepository->find((int)$id);

        if ($dataset === null) {
            http_response_code(404);
            echo '404 - Dataset Not Found';
        return;
        }

        $benchmarkRepository = new Benchmark($db);
        $benchmarks = $benchmarkRepository->forDataset((int)$id);

        require __DIR__ . '/../Views/dataset.php';

        }
}
