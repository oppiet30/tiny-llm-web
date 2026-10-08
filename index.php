<?php

require __DIR__ . '/config.php';

$sql = "
    SELECT
        b.run_id,
        m.hostname,
        m.cpu_model,
        mo.name AS model_name,
        d.name AS dataset_name,
        b.training_steps,
        b.real_seconds,
        b.train_loss,
        b.validation_loss,
        b.run_date,
        b.training_steps / b.real_seconds AS steps_per_second
    FROM benchmark_runs b
    JOIN machines m
        ON m.machine_id = b.machine_id
    JOIN models mo
        ON mo.model_id = b.model_id
    JOIN datasets d
        ON d.dataset_id = b.dataset_id
    ORDER BY steps_per_second DESC
";

$result = $db->query($sql);

if (!$result) {
    die('Query failed: ' . $db->error);
}

function runtime(float $seconds): string
{
    $minutes = floor($seconds / 60);
    $remaining = $seconds - ($minutes * 60);

    return sprintf('%02d:%06.3f', $minutes, $remaining);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tiny LLM Benchmarks</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<main>
    <h1>Tiny LLM Benchmarks</h1>

    <p>
        CPU training benchmarks for the TinyGPT project.
    </p>

    <table>
        <thead>
        <tr>
            <th>Rank</th>
            <th>Machine</th>
            <th>CPU</th>
            <th>Model</th>
            <th>Steps</th>
            <th>Runtime</th>
            <th>Steps/sec</th>
            <th>Train Loss</th>
            <th>Validation Loss</th>
        </tr>
        </thead>

        <tbody>

        <?php $rank = 1; ?>

        <?php while ($row = $result->fetch_assoc()): ?>

            <tr>
                <td><?= $rank++ ?></td>

                <td>
                    <?= htmlspecialchars($row['hostname']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['cpu_model']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['model_name']) ?>
                </td>

                <td>
                    <?= number_format($row['training_steps']) ?>
                </td>

                <td>
                    <?= runtime((float)$row['real_seconds']) ?>
                </td>

                <td>
                    <?= number_format(
                        (float)$row['steps_per_second'],
                        3
                    ) ?>
                </td>

                <td>
                    <?= number_format(
                        (float)$row['train_loss'],
                        4
                    ) ?>
                </td>

                <td>
                    <?= number_format(
                        (float)$row['validation_loss'],
                        4
                    ) ?>
                </td>
            </tr>

        <?php endwhile; ?>

        </tbody>
    </table>

</main>

</body>
</html>

