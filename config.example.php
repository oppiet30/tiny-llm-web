<?php

$db = new mysqli(
    'localhost',       // MariaDB server
    'root',
    'your-password-here',
    'tiny_llm_benchmarks'
);

if ($db->connect_errno) {
    die('Database connection failed: ' . $db->connect_error);
}

$db->set_charset('utf8mb4');

