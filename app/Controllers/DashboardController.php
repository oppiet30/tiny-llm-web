<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Benchmark;
use App\Models\Machine;

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
        require __DIR__ . '/../../config.php';

        $model = new Machine($db);
        $machines = $model->all();

        require __DIR__ . '/../Views/machines.php';
    }
}
