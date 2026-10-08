<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Machine;
use App\Models\Benchmark;

class MachineController
{
    public function show(string $id): void
    {
        if (!ctype_digit($id) || (int)$id < 1) {
            http_response_code(404);
            echo '404 - Machine Not Found';
            return;
        }

        require __DIR__ . '/../../config.php';

        $model = new Machine($db);
        $machine = $model->find((int)$id);

        if ($machine === null) {
            http_response_code(404);
            echo '404 - Machine Not Found';
            return;
        }
        $benchmarkModel = new Benchmark($db);
        $benchmarks = $benchmarkModel->forMachine((int)$id);

        require __DIR__ . '/../Views/machine.php';
    }
}
