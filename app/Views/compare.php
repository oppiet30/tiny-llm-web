<?php
declare(strict_types=1);

$escape = static fn(mixed $value): string => htmlspecialchars(
    (string)($value ?? ''),
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);
$baseUrl = $escape(BASE_PATH);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Compare Benchmarks - Tiny LLM</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/style.css">
</head>
<body>
<nav class="dashboard-nav">
    <?php require __DIR__ . '/partials/navigation.php'; ?>
</nav>
<main>
    <h1>Compare Benchmarks</h1>
    <p>Compare runs with the same dataset, model, and total training-step count.
       Repeated runs are listed individually.</p>

    <form action="<?= $baseUrl ?>/compare" method="get">
        <label for="dataset_id">Dataset</label>
        <select name="dataset_id" id="dataset_id">
            <?php foreach ($datasets as $dataset): ?>
                <option value="<?= (int)$dataset['dataset_id'] ?>"
                    <?= (int)$datasetId === (int)$dataset['dataset_id'] ? 'selected' : '' ?>>
                    <?= $escape($dataset['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="model_id">Model</label>
        <select name="model_id" id="model_id">
            <?php foreach ($models as $model): ?>
                <option value="<?= (int)$model['model_id'] ?>"
                    <?= (int)$modelId === (int)$model['model_id'] ? 'selected' : '' ?>>
                    <?= $escape($model['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="training_steps">Training steps</label>
        <input type="number" id="training_steps" name="training_steps"
               min="1" step="1" required value="<?= (int)$trainingSteps ?>">
        <button type="submit">Compare</button>
    </form>

    <h2>Matching runs (<?= count($benchmarks) ?>)</h2>

    <?php if (empty($benchmarks)): ?>
        <p>No benchmark runs match these filters.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Run</th>
                    <th>Machine</th>
                    <th>CPU</th>
                    <th>Runtime</th>
                    <th>Steps/sec</th>
                    <th>Train Loss</th>
                    <th>Validation Loss</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($benchmarks as $index => $run): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <a href="<?= $baseUrl ?>/runs/<?= (int)$run['run_id'] ?>">
                                #<?= (int)$run['run_id'] ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?= $baseUrl ?>/machines/<?= (int)$run['machine_id'] ?>">
                                <?= $escape($run['hostname']) ?>
                            </a>
                        </td>
                        <td><?= $escape($run['cpu_model']) ?></td>
                        <td><?= $run['real_seconds'] !== null
                            ? $escape(formatDuration((float)$run['real_seconds']))
                            : 'N/A' ?></td>
                        <td><?= $run['steps_per_second'] !== null
                            ? number_format((float)$run['steps_per_second'], 3)
                            : 'N/A' ?></td>
                        <td><?= $run['train_loss'] !== null
                            ? number_format((float)$run['train_loss'], 4)
                            : 'N/A' ?></td>
                        <td><?= $run['validation_loss'] !== null
                            ? number_format((float)$run['validation_loss'], 4)
                            : 'N/A' ?></td>
                        <td><?= $escape($run['run_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
</body>
</html>
