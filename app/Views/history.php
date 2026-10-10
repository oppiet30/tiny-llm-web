<?php
declare(strict_types=1);

$escape = static fn(mixed $value): string => htmlspecialchars(
    (string) ($value ?? ''),
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);
$baseUrl = $escape(BASE_PATH);
$points = [];
foreach ($history as $run) {
    if ($run['steps_per_second'] === null || !is_numeric($run['steps_per_second'])
        || !is_finite((float) $run['steps_per_second']) || (float) $run['steps_per_second'] <= 0) {
        continue;
    }
    $points[] = [
        'date' => (string) $run['run_date'],
        'hostname' => (string) $run['hostname'],
        'machine_id' => (int) $run['machine_id'],
        'run_id' => (int) $run['run_id'],
        'steps_per_second' => (float) $run['steps_per_second'],
    ];
}
$maxRate = $points === [] ? 0.0 : max(array_column($points, 'steps_per_second'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Benchmark History - Tiny LLM</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/style.css">
</head>
<body>
<nav class="dashboard-nav">
    <?php require __DIR__ . '/partials/navigation.php'; ?>
</nav>
<main>
    <h1>Benchmark History</h1>
    <p>Explore recorded throughput over time. Each point represents one benchmark run; filters keep comparisons within the selected dataset and model.</p>

    <form action="<?= $baseUrl ?>/history" method="get">
        <label for="dataset_id">Dataset</label>
        <select id="dataset_id" name="dataset_id">
            <option value="">All datasets</option>
            <?php foreach ($datasets as $dataset): ?>
                <option value="<?= (int) $dataset['dataset_id'] ?>"
                    <?= $datasetId === (int) $dataset['dataset_id'] ? 'selected' : '' ?>>
                    <?= $escape($dataset['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="model_id">Model</label>
        <select id="model_id" name="model_id">
            <option value="">All models</option>
            <?php foreach ($models as $model): ?>
                <option value="<?= (int) $model['model_id'] ?>"
                    <?= $modelId === (int) $model['model_id'] ? 'selected' : '' ?>>
                    <?= $escape($model['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="machine_id">Machine</label>
        <select id="machine_id" name="machine_id">
            <option value="">All machines</option>
            <?php foreach ($machines as $machine): ?>
                <option value="<?= $machine['machine_id'] ?>"
                    <?= $machineId === $machine['machine_id'] ? 'selected' : '' ?>>
                    <?= $escape($machine['hostname']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filter history</button>
    </form>

    <?php if ($points === []): ?>
        <p>No valid benchmark throughput measurements match these filters.</p>
    <?php else: ?>
        <section aria-labelledby="history-chart-heading" class="benchmark-statistics">
            <h2 id="history-chart-heading">Throughput history</h2>
            <p>Bars are scaled to the fastest displayed run. Runs are ordered chronologically in the table below.</p>
            <div class="throughput-chart">
                <?php foreach ($points as $point): ?>
                    <?php $width = $maxRate > 0 ? $point['steps_per_second'] / $maxRate * 100 : 0; ?>
                    <div class="throughput-row">
                        <div class="throughput-label">
                            <span><?= $escape($point['date']) ?> · <?= $escape($point['hostname']) ?> · Run #<?= $point['run_id'] ?></span>
                            <strong><?= number_format($point['steps_per_second'], 3) ?> steps/sec</strong>
                        </div>
                        <div class="throughput-track" role="img"
                             aria-label="Run <?= $point['run_id'] ?> throughput <?= number_format($point['steps_per_second'], 3) ?> steps per second">
                            <div class="throughput-bar" style="width: <?= number_format($width, 4, '.', '') ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Date</th><th>Machine</th><th>Run</th><th>Steps/sec</th></tr></thead>
                <tbody>
                <?php foreach ($points as $point): ?>
                    <tr>
                        <td><?= $escape($point['date']) ?></td>
                        <td><a href="<?= $baseUrl ?>/machines/<?= $point['machine_id'] ?>"><?= $escape($point['hostname']) ?></a></td>
                        <td><a href="<?= $baseUrl ?>/runs/<?= $point['run_id'] ?>">#<?= $point['run_id'] ?></a></td>
                        <td><?= number_format($point['steps_per_second'], 3) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
