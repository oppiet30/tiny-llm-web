<?php
declare(strict_types=1);

$escape = static fn(mixed $value): string =>
    htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$sections = [
    'Machine Information' => [
        'hostname' => 'Hostname',
        'cpu_model' => 'Processor',
    ],

    'Model and Dataset' => [
        'model_name' => 'Model',
        'dataset_name' => 'Dataset',
    ],

    'Training Configuration' => [
        'start_step' => 'Starting Step',
        'training_steps' => 'Target Training Steps',
        'steps_this_run' => 'Steps This Run',
        'batch_size' => 'Batch Size',
        'learning_rate' => 'Learning Rate',
        'pytorch_version' => 'PyTorch Version',
        'pytorch_threads' => 'PyTorch Threads',
        'interop_threads' => 'Interop Threads',
    ],

    'Training Results' => [
        'train_loss' => 'Training Loss',
        'validation_loss' => 'Validation Loss',
        'real_seconds' => 'Runtime (seconds)',
        'steps_per_second' => 'Steps per Second',
        'run_date' => 'Run Date',
        'notes' => 'Notes',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Benchmark #<?= $escape($run['run_id']) ?> - Tiny LLM</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8') ?>/css/style.css">
</head>
<body>
    <main>
        <h1>Benchmark Run #<?= $escape($run['run_id']) ?></h1>

        <p>
            <?php require __DIR__ . '/partials/navigation.php'; ?>
            <span class="nav-separator">|</span>
            <a href="<?= $escape(BASE_PATH) ?>/api/v1/runs/<?= (int)$run['run_id'] ?>">
                View JSON
            </a>
        </p>
<?php foreach ($sections as $sectionTitle => $fields): ?>

    <section>
        <h2><?= $escape($sectionTitle) ?></h2>

        <table>
            <tbody>
                <?php foreach ($fields as $field => $label): ?>
                    <?php if (array_key_exists($field, $run)): ?>
                        <tr>
                            <th scope="row">
                                <?= $escape($label) ?>
                            </th>
                            <td>
                                <?php
                                $value = $run[$field];

                                if ($value === null) {
                                    $displayValue = 'N/A';
                                } elseif (in_array($field, [
                                    'start_step',
                                    'training_steps',
                                    'steps_this_run',
                                    'batch_size',
                                    'pytorch_threads',
                                    'interop_threads'
                                ], true)) {
                                    $displayValue = number_format((int) $value);
                                } elseif ($field === 'real_seconds') {
                                    $displayValue = formatDuration((float) $value);
                                } elseif ($field === 'steps_per_second') {
                                    $displayValue = number_format((float) $value, 3) . ' steps/sec';
                                } elseif (in_array($field, ['train_loss', 'validation_loss'], true)) {
                                    $displayValue = number_format((float) $value, 4);
                                } else {
                                    $displayValue = (string) $value;
                                }
                                ?>

                            <?php if ($field === 'hostname'): ?>
                                <a href="<?= $escape(BASE_PATH) ?>/machines/<?= (int)$run['machine_id'] ?>">
                                    <?= $escape($displayValue) ?>
                                </a>
                           <?php elseif ($field === 'model_name'): ?>
                               <a href="<?= $escape(BASE_PATH) ?>/models/<?= (int)$run['model_id'] ?>">
                                   <?= $escape($displayValue) ?>
                               </a>
                           <?php elseif ($field === 'dataset_name'): ?>
                               <a href="<?= $escape(BASE_PATH) ?>/datasets/<?= (int)$run['dataset_id'] ?>">
                                   <?= $escape($displayValue) ?>
                               </a>
                           <?php else: ?>
                               <?= $escape($displayValue) ?>
                           <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

<?php endforeach; ?>
    </main>
</body>
</html>
