<?php
declare(strict_types=1);

$escape = static fn(mixed $value): string =>
    htmlspecialchars(
        $value === null ? 'N/A' : (string)$value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );

$number = static fn(mixed $value): string =>
    $value === null ? 'N/A' : number_format((int)$value);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Datasets - Tiny LLM</title>
    <link rel="stylesheet" href="<?= $escape(BASE_PATH) ?>/css/style.css">
</head>
<body>
<main>
    <h1>Training Datasets</h1>

    <nav>
        <?php require __DIR__ . '/partials/navigation.php'; ?>
    </nav>

    <h2>Dataset Inventory</h2>

    <?php if (empty($datasets)): ?>
        <p>No datasets have been recorded.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Dataset</th>
                    <th>Total Characters</th>
                    <th>Training Characters</th>
                    <th>Validation Characters</th>
                    <th>Vocabulary Size</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datasets as $dataset): ?>
                    <tr>
                        <td>
                            <a href="<?= $escape(BASE_PATH) ?>/datasets/<?= (int)$dataset['dataset_id'] ?>">
                                <?= $escape($dataset['name']) ?>
                            </a>
                        </td>
                        <td><?= $number($dataset['total_characters']) ?></td>
                        <td><?= $number($dataset['training_characters']) ?></td>
                        <td><?= $number($dataset['validation_characters']) ?></td>
                        <td><?= $number($dataset['vocabulary_size']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
</body>
</html>
