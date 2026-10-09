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
    <title>Models - Tiny LLM</title>
    <link rel="stylesheet" href="<?= $escape(BASE_PATH) ?>/css/style.css">
</head>
<body>
<main>
    <h1>LLM Models</h1>

    <nav>
        <?php require __DIR__ . '/partials/navigation.php'; ?>
    </nav>

    <h2>Model Inventory</h2>

    <?php if (empty($models)): ?>
        <p>No models have been recorded.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Model</th>
                    <th>Parameters</th>
                    <th>Embedding</th>
                    <th>Heads</th>
                    <th>Layers</th>
                    <th>Context</th>
                    <th>Tokenizer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($models as $model): ?>
                    <tr>
                        <td>
                            <a href="<?= $escape(BASE_PATH) ?>/models/<?= (int)$model['model_id'] ?>">
                                <?= $escape($model['name']) ?>
                            </a>
                        </td>
                        <td><?= $number($model['parameter_count']) ?></td>
                        <td><?= $number($model['n_embd']) ?></td>
                        <td><?= $number($model['n_head']) ?></td>
                        <td><?= $number($model['n_layer']) ?></td>
                        <td><?= $number($model['block_size']) ?></td>
                        <td><?= $escape($model['tokenizer']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
</body>
</html>
