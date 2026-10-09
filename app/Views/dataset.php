<?php
declare(strict_types=1);

$escape = static fn(mixed $value): string =>
    htmlspecialchars(
        $value === null ? 'N/A' : (string)$value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );

$fields = [
    'dataset_id' => 'Dataset ID',
    'name' => 'Dataset Name',
    'total_characters' => 'Total Characters',
    'training_characters' => 'Training Characters',
    'validation_characters' => 'Validation Characters',
    'vocabulary_size' => 'Vocabulary Size',
    'notes' => 'Notes',
    'created_at' => 'Date Added',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $escape($dataset['name']) ?> - Tiny LLM</title>
    <link rel="stylesheet" href="<?= $escape(BASE_PATH) ?>/css/style.css">
</head>
<body>
<main>
    <h1><?= $escape($dataset['name']) ?></h1>

    <nav>
        <?php require __DIR__ . '/partials/navigation.php'; ?>
    </nav>

    <h2>Dataset Specifications</h2>

    <table>
        <tbody>
            <?php foreach ($fields as $field => $label): ?>
                <?php
                $value = $dataset[$field] ?? null;

                if ($value === null) {
                    $display = 'N/A';
                } elseif (in_array($field, [
                    'total_characters',
                    'training_characters',
                    'validation_characters',
                    'vocabulary_size'
                ], true)) {
                    $display = number_format((int)$value);
                } else {
                    $display = (string)$value;
                }
                ?>
                <tr>
                    <th scope="row"><?= $escape($label) ?></th>
                    <td><?= $escape($display) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<h2>Benchmark History</h2>

<?php if (empty($benchmarks)): ?>
    <p>No benchmark runs recorded for this dataset.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Run</th>
                <th>Machine</th>
                <th>Model</th>
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

                    <td>
                        <a href="<?= $escape(BASE_PATH) ?>/models/<?= (int)$run['model_id'] ?>">
                            <?= $escape($run['model_name']) ?>
                        </a>
                    </td>

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
