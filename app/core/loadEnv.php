<?php
function loadEnv($path) {
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!isset($_ENV[$name])) {
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

// Roep aan in je init.php
loadEnv(__DIR__ . '/../../.env');
