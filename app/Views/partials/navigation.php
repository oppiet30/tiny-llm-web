<?php
declare(strict_types=1);

$baseUrl = htmlspecialchars(
    BASE_PATH,
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);
?>
    <a href="<?= $baseUrl ?>/">Dashboard</a>
    <a href="<?= $baseUrl ?>/machines">Machines</a>
    <a href="<?= $baseUrl ?>/models">Models</a>
    <a href="<?= $baseUrl ?>/datasets">Datasets</a>
    <a href="<?= $baseUrl ?>/compare">Compare</a>
    <a href="<?= $baseUrl ?>/history">History</a>
