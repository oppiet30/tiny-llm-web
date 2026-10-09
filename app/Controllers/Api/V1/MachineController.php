<?php
declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Models\Machine;
use App\Models\Benchmark;

class MachineController
{
    public function runs(string $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!ctype_digit($id) || (int)$id < 1) {
            http_response_code(404);
            echo json_encode(['error' => 'Machine not found']);
            return;
        }

        require __DIR__ . '/../../../../config.php';

        $machineModel = new Machine($db);
        $machine = $machineModel->find((int)$id);

        if ($machine === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Machine not found']);
            return;
        }

        $benchmarkModel = new Benchmark($db);
        $runs = $benchmarkModel->forMachine((int)$id);

        echo json_encode(
            [
                'machine_id' => (int)$id,
                'hostname' => $machine['hostname'],
                'count' => count($runs),
                'data' => $runs
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );
    }
    public function index(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        require __DIR__ . '/../../../../config.php';

        $model = new Machine($db);
        $machines = $model->all();

        echo json_encode(
            [
                'data' => $machines,
                'count' => count($machines)
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );
    }
    public function show(string $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!ctype_digit($id) || (int)$id < 1) {
            http_response_code(404);
            echo json_encode(['error' => 'Machine not found']);
            return;
        }

        require __DIR__ . '/../../../../config.php';

        $model = new Machine($db);
        $machine = $model->find((int)$id);

        if ($machine === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Machine not found']);
            return;
        }

        echo json_encode(
            ['data' => $machine],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );
    }
}
