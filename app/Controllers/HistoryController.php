<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Benchmark;
use App\Models\Dataset;
use App\Models\Model;

final class HistoryController
{
    public function index(): void
    {
        require __DIR__ . '/../../config.php';

        $datasets = (new Dataset($db))->all();
        $models = (new Model($db))->all();

        $datasetId = filter_input(INPUT_GET, 'dataset_id', FILTER_VALIDATE_INT);
        $modelId = filter_input(INPUT_GET, 'model_id', FILTER_VALIDATE_INT);
        $machineId = filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);

        $datasetId = $datasetId !== null && $datasetId !== false && $datasetId > 0 ? $datasetId : null;
        $modelId = $modelId !== null && $modelId !== false && $modelId > 0 ? $modelId : null;
        $machineId = $machineId !== null && $machineId !== false && $machineId > 0 ? $machineId : null;

        $history = (new Benchmark($db))->history($datasetId, $modelId, $machineId);
        $machines = [];
        foreach ($history as $run) {
            $machines[(int) $run['machine_id']] = [
                'machine_id' => (int) $run['machine_id'],
                'hostname' => (string) $run['hostname'],
            ];
        }
        $machines = array_values($machines);

        require __DIR__ . '/../Views/history.php';
    }
}
