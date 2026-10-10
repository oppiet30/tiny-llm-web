<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = new mysqli(
    'localhost',       // MariaDB server
    'root',
    'your-password-here',
    'tiny_llm_benchmarks'
);

$db->set_charset('utf8mb4');

