<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Benchmark;
use App\Models\Dataset;
use App\Models\Model;

class CompareController
{
    public function index(): void
    {
        require __DIR__ . '/../../config.php';

        $datasets = (new Dataset($db))->all();
        $models = (new Model($db))->all();

        // Default to the Gutenberg 10k-step group when available.
        $defaultDatasetId = null;
        foreach ($datasets as $dataset) {
            if ($dataset['name'] === 'Gutenberg 50 MiB Corpus') {
                $defaultDatasetId = (int) $dataset['dataset_id'];
                break;
            }
        }

        $defaultModelId = null;
        foreach ($models as $model) {
            if ($model['name'] === 'TinyGPT-853K') {
                $defaultModelId = (int) $model['model_id'];
                break;
            }
        }

        $datasetId = filter_input(INPUT_GET, 'dataset_id', FILTER_VALIDATE_INT);
        $modelId = filter_input(INPUT_GET, 'model_id', FILTER_VALIDATE_INT);
        $trainingSteps = filter_input(INPUT_GET, 'training_steps', FILTER_VALIDATE_INT);

        $datasetId = $datasetId !== null && $datasetId !== false && $datasetId > 0
            ? $datasetId : $defaultDatasetId;
        $modelId = $modelId !== null && $modelId !== false && $modelId > 0
            ? $modelId : $defaultModelId;
        $trainingSteps = $trainingSteps !== null && $trainingSteps !== false && $trainingSteps > 0
            ? $trainingSteps : 10000;

        $benchmarks = [];
        if ($datasetId !== null && $modelId !== null) {
            $benchmarks = (new Benchmark($db))->compare($datasetId, $modelId, $trainingSteps);
        }

        require __DIR__ . '/../Views/compare.php';
    }
}
