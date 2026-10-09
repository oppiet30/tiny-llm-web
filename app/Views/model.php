<?php
declare(strict_types=1);

$escape = static fn(mixed $value): string =>
    htmlspecialchars(
        $value === null ? 'N/A' : (string)$value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );

$fields = [
    'model_id' => 'Model ID',
    'name' => 'Model Name',
    'parameter_count' => 'Parameters',
    'n_embd' => 'Embedding Dimension',
    'n_head' => 'Attention Heads',
    'n_layer' => 'Transformer Layers',
    'block_size' => 'Context Length',
    'dropout' => 'Dropout',
    'tokenizer' => 'Tokenizer',
    'notes' => 'Notes',
    'created_at' => 'Date Added',
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $escape($model['name']) ?> - Tiny LLM</title>
    <link rel="stylesheet" href="<?= $escape(BASE_PATH) ?>/css/style.css">
</head>
<body>
<main>
    <h1><?= $escape($model['name']) ?></h1>

    <nav>
        <?php require __DIR__ . '/partials/navigation.php'; ?>
    </nav>

    <h2>Model Specifications</h2>

    <table>
        <tbody>
            <?php foreach ($fields as $field => $label): ?>
                <tr>
                    <th scope="row"><?= $escape($label) ?></th>
                    <td>
                        <?php
                        $value = $model[$field] ?? null;

                        if ($value === null) {
                            $display = 'N/A';
                        } elseif (in_array($field, [
                            'parameter_count',
                            'n_embd',
                            'n_head',
                            'n_layer',
                            'block_size'
                        ], true)) {
                            $display = number_format((int)$value);
                        } else {
                            $display = (string)$value;
                        }
                        ?>
                        <?= $escape($display) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<h2>Benchmark History</h2>

<?php if (empty($benchmarks)): ?>
    <p>No benchmark runs recorded for this model.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Run</th>
                <th>Machine</th>
                <th>Dataset</th>
                <th>Steps</th>
                <th>Runtime</th>
                <th>Steps/sec</th>
                <th>Train Loss</th>
                <th>Validation Loss</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($benchmarks as $run): ?>
                <tr>
                    <td>
                        <a href="<?= $escape(BASE_PATH) ?>/runs/<?= (int)$run['run_id'] ?>">
                            #<?= (int)$run['run_id'] ?>
                        </a>
                    </td>

                    <td>
                        <a href="<?= $escape(BASE_PATH) ?>/machines/<?= (int)$run['machine_id'] ?>">
                            <?= $escape($run['hostname']) ?>
                        </a>
                    </td>

                    <td><?= $escape($run['dataset_name']) ?></td>

                    <td>
                        <?= number_format((int)(
                            $run['steps_this_run']
                            ?? $run['training_steps']
                        )) ?>
                    </td>

                    <td>
                        <?= $run['real_seconds'] !== null
                            ? $escape(formatDuration((float)$run['real_seconds']))
                            : 'N/A' ?>
                    </td>

                    <td>
                        <?= $run['steps_per_second'] !== null
                            ? number_format((float)$run['steps_per_second'], 3)
                            : 'N/A' ?>
                    </td>

                    <td>
                        <?= $run['train_loss'] !== null
                            ? number_format((float)$run['train_loss'], 4)
                            : 'N/A' ?>
                    </td>

                    <td>
                        <?= $run['validation_loss'] !== null
                            ? number_format((float)$run['validation_loss'], 4)
                            : 'N/A' ?>
                    </td>

                    <td><?= $escape($run['run_date']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</main>
</body>
</html>
