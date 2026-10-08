<?php
declare(strict_types=1);

function displayValue(mixed $value): string
{
    return htmlspecialchars(
        $value === null || $value === '' ? 'N/A' : (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= displayValue($machine['hostname']) ?> - Tiny LLM</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<main>
    <h1><?= displayValue($machine['hostname']) ?></h1>

    <nav>
        <a href="../">Dashboard</a>
        <a href="../machines">Machines</a>
    </nav>

    <h2>Hardware Specifications</h2>

    <table>
        <tbody>
            <tr>
                <th>Hostname</th>
                <td><?= displayValue($machine['hostname']) ?></td>
            </tr>
            <tr>
                <th>CPU Model</th>
                <td><?= displayValue($machine['cpu_model']) ?></td>
            </tr>
            <tr>
                <th>CPU Cores</th>
                <td><?= displayValue($machine['cpu_cores']) ?></td>
            </tr>
            <tr>
                <th>CPU Threads</th>
                <td><?= displayValue($machine['cpu_threads']) ?></td>
            </tr>
            <tr>
                <th>RAM</th>
                <td>
                    <?= $machine['ram_mb'] !== null
                        ? number_format($machine['ram_mb'] / 1024, 1) . ' GB'
                        : 'N/A' ?>
                </td>
            </tr>
            <tr>
                <th>Operating System</th>
                <td><?= displayValue($machine['operating_system']) ?></td>
            </tr>
            <tr>
                <th>Notes</th>
                <td><?= displayValue($machine['notes']) ?></td>
            </tr>
        </tbody>
    </table>

<h2>Benchmark History</h2>

<?php if (empty($benchmarks)): ?>
    <p>No benchmark runs recorded for this machine.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Run</th>
                <th>Dataset</th>
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
                <td><?= (int)$run['run_id'] ?></td>
                <td><?= displayValue($run['dataset_name']) ?></td>
                <td><?= displayValue($run['model_name']) ?></td>
                <td><?= number_format((int)$run['training_steps']) ?></td>
                <td><?= number_format((float)$run['real_seconds'], 3) ?> s</td>
                <td>
                    <?= $run['steps_per_second'] !== null
                        ? number_format((float)$run['steps_per_second'], 3)
                        : 'N/A' ?>
                </td>
                <td><?= number_format((float)$run['train_loss'], 4) ?></td>
                <td><?= number_format((float)$run['validation_loss'], 4) ?></td>
                <td><?= displayValue($run['run_date']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</main>
</body>
</html>
