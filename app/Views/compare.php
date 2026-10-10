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

        <label for="chart_mode">Chart view</label>
        <select name="chart_mode" id="chart_mode">
            <option value="average" <?= $chartMode === 'average' ? 'selected' : '' ?>>Machine averages</option>
            <option value="runs" <?= $chartMode === 'runs' ? 'selected' : '' ?>>Individual runs</option>
        </select>

        <label for="training_steps">Training steps</label>
        <input type="number" id="training_steps" name="training_steps"
               min="1" step="1" required value="<?= (int)$trainingSteps ?>">
        <button type="submit">Compare</button>
    </form>

    <?php if (!empty($benchmarks)): ?>
        <section class="benchmark-statistics" aria-labelledby="comparison-chart-heading">
            <h2 id="comparison-chart-heading"><?= $chartMode === 'runs' ? 'Individual run throughput' : 'Machine average throughput' ?></h2>
            <?php if ($chartMode === 'runs'): ?>
                <p>Each bar represents one matching benchmark run. Bars are scaled to the fastest run.</p>
                <?php
                $validRuns = array_values(array_filter($benchmarks, static fn(array $run): bool =>
                    isset($run['steps_per_second']) && is_numeric($run['steps_per_second'])
                    && is_finite((float) $run['steps_per_second']) && (float) $run['steps_per_second'] > 0
                ));
                $maxRunThroughput = $validRuns === [] ? 0.0 : max(array_map(
                    static fn(array $run): float => (float) $run['steps_per_second'],
                    $validRuns
                ));
                ?>
                <?php if ($validRuns === []): ?>
                    <p>No valid per-run throughput measurements are available for this comparison.</p>
                <?php else: ?>
                    <div class="throughput-chart">
                        <?php foreach ($validRuns as $run): ?>
                            <?php $width = $maxRunThroughput > 0 ? ((float) $run['steps_per_second'] / $maxRunThroughput) * 100 : 0; ?>
                            <div class="throughput-row">
                                <div class="throughput-label">
                                    <span><a href="<?= $baseUrl ?>/runs/<?= (int) $run['run_id'] ?>">Run #<?= (int) $run['run_id'] ?></a> · <?= $escape($run['hostname']) ?></span>
                                    <strong><?= number_format((float) $run['steps_per_second'], 3) ?> steps/sec</strong>
                                </div>
                                <div class="throughput-track" role="img" aria-label="Run <?= (int) $run['run_id'] ?> throughput <?= number_format((float) $run['steps_per_second'], 3) ?> steps per second">
                                    <div class="throughput-bar" style="width: <?= number_format($width, 4, '.', '') ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p>Average throughput across matching runs. Bars are scaled to the fastest machine.</p>
                <?php if (!empty($statistics)): ?>
                    <?php $maxMean = max(array_column($statistics, 'mean')); ?>
                    <div class="throughput-chart">
                        <?php foreach ($statistics as $stat): ?>
                            <?php $barWidth = $maxMean > 0 ? ($stat['mean'] / $maxMean) * 100 : 0; ?>
                            <div class="throughput-row">
                                <div class="throughput-label">
                                    <span><?= $escape($stat['hostname']) ?></span>
                                    <strong><?= number_format($stat['mean'], 3) ?> steps/sec</strong>
                                </div>
                                <div class="throughput-track" role="img" aria-label="<?= $escape($stat['hostname']) ?> average throughput <?= number_format($stat['mean'], 3) ?> steps per second">
                                    <div class="throughput-bar" style="width: <?= number_format($barWidth, 4, '.', '') ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No valid machine-average measurements are available for this comparison.</p>
                <?php endif; ?>
            <?php endif; ?>
        </section>
        <?php if (!empty($statistics)): ?>
        <section class="benchmark-statistics" aria-labelledby="repeat-statistics-heading">
            <h2 id="repeat-statistics-heading">Repeat-run statistics</h2>
            <p>Standard deviation is the sample standard deviation and is unavailable when a machine has only one valid run.</p>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Machine</th>
                            <th>Runs</th>
                            <th>Average steps/sec</th>
                            <th>Minimum</th>
                            <th>Maximum</th>
                            <th>Std. deviation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($statistics as $stat): ?>
                            <tr>
                                <td><?= $escape($stat['hostname']) ?></td>
                                <td><?= (int) $stat['count'] ?></td>
                                <td><?= number_format($stat['mean'], 3) ?></td>
                                <td><?= number_format($stat['min'], 3) ?></td>
                                <td><?= number_format($stat['max'], 3) ?></td>
                                <td><?= $stat['stddev'] !== null ? number_format($stat['stddev'], 3) : 'N/A' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>
    <?php endif; ?>

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
