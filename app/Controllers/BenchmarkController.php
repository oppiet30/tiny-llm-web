<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Benchmark;

class BenchmarkController
{
    public function show(string $id): void
    {
        if (!ctype_digit($id) || (int)$id < 1) {
            http_response_code(404);
            echo 'Benchmark run not found';
            return;
        }

        require __DIR__ . '/../../config.php';

        $model = new Benchmark($db);
        $run = $model->find((int)$id);

        if ($run === null) {
            http_response_code(404);
            echo 'Benchmark run not found';
            return;
        }

        require __DIR__ . '/../Views/benchmark.php';
    }
}
