<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Benchmark;

class DashboardController
{
    public function index(): void
    {
        require __DIR__ . '/../../config.php';

        $model = new Benchmark($db);
        $benchmarks = $model->all();

        require __DIR__ . '/../Views/dashboard.php';
    }

    public function machines(): void
    {
        echo 'Tiny LLM Machine List';
    }
}
