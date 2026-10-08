<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Machines - Tiny LLM Benchmarks</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<main>
    <h1>Benchmark Machines</h1>

    <nav>
        <a href="./">Dashboard</a>
        <a href="machines">Machines</a>
    </nav>

    <p>Hardware used for TinyGPT training benchmarks.</p>

    <table>
        <thead>
            <tr>
                <th>Hostname</th>
                <th>CPU</th>
                <th>Cores</th>
                <th>Threads</th>
                <th>RAM</th>
                <th>Operating System</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($machines as $machine): ?>
            <tr>
                <td><?= htmlspecialchars($machine['hostname']) ?></td>
                <td><?= htmlspecialchars($machine['cpu_model']) ?></td>
                <td><?= htmlspecialchars((string)($machine['cpu_cores'] ?? 'N/A')) ?></td>
                <td><?= htmlspecialchars((string)($machine['cpu_threads'] ?? 'N/A')) ?></td>
                <td>
                    <?= $machine['ram_mb'] !== null
                        ? number_format($machine['ram_mb'] / 1024, 1) . ' GB'
                        : 'N/A' ?>
                </td>
                <td><?= htmlspecialchars($machine['operating_system'] ?? 'N/A') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>
