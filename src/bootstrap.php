<?php


$root = dirname(__DIR__);
$envFile = $root . "/.env";
if (is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === "" || str_starts_with($line, "#")) {
                continue;
            }
            if (!str_contains($line, "=")) {
                continue;
            }
            [$k, $v] = array_map("trim", explode("=", $line, 2));
            if ($k === "") {
                continue;
            }
            putenv($k . "=" . $v);
            $_ENV[$k] = $v;
        }
    }
}

spl_autoload_register(
    static function (string $class) use ($root): void {
        $prefix = "App\\";
        if (!str_starts_with($class, $prefix)) {
            return;
        }
        $relative = substr($class, strlen($prefix));
        $path = $root . "/src/" . str_replace("\\", "/", $relative) . ".php";
        if (is_file($path)) {
            require $path;
        }
    },
);
