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

        <?php foreach ($benchmarks as $row): ?>

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

        <?php endforeach; ?>

        </tbody>
    </table>

</main>

</body>
</html>

