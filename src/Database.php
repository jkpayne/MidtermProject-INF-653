<?php


namespace App;

use PDO;
use PDOException;

/**
 * Shared PDO connection for repositories (PostgreSQL).
 */
final class Database {
    private static ?PDO $pdo = null;

    /**
     * Get or create a PDO connection.
     * Supports Heroku DATABASE_URL or individual env vars.
     * @returns PDO Configured PDO instance.
     * @throws PDOException if connection fails.
     */
    public static function pdo(): PDO {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        // Check for Heroku DATABASE_URL first
        $databaseUrl = self::env("DATABASE_URL", "");
        if ($databaseUrl !== "") {
            self::$pdo = self::createPdoFromUrl($databaseUrl);
            return self::$pdo;
        }

        // Fall back to individual env vars with a fallback to sensible defaults
        $host = self::env("DB_HOST", "127.0.0.1");
        $port = self::env("DB_PORT", "5432");
        $name = self::env("DB_NAME", "quotesdb");
        $user = self::env("DB_USER", "postgres");
        $pass = self::env("DB_PASS", "");

        $dsn = sprintf("pgsql:host=%s;port=%s;dbname=%s", $host, $port, $name);

        self::$pdo = new PDO(
            $dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
        );

        return self::$pdo;
    }

    /**
     * Get environment variable with fallback.
     *
     * @param string $key Environment variable name.
     * @param string $default Fallback when unset.
     *
     * @returns string Environment value or default.
     */
    private static function env(string $key, string $default): string {
        $v = getenv($key);
        if ($v !== false && $v !== "") {
            return $v;
        }

        if (isset($_ENV[$key]) && $_ENV[$key] !== "") {
            return (string)$_ENV[$key];
        }

        return $default;
    }

    /**
     * Parse DATABASE_URL and create PDO instance (Heroku format).
     *
     * @param string $url Database URL.
     *
     * @returns PDO Configured PDO instance.
     */
    private static function createPdoFromUrl(string $url): PDO {
        $parts = parse_url($url);
        $host = $parts["host"] ?? "127.0.0.1";
        $port = $parts["port"] ?? 5432;
        $dbname = isset($parts["path"]) ? ltrim($parts["path"], "/") : "quotesdb";
        $user = $parts["user"] ?? "postgres";
        $pass = $parts["pass"] ?? "";

        $dsn = sprintf("pgsql:host=%s;port=%s;dbname=%s", $host, $port, $dbname);

        return new PDO(
            $dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
        );
    }
}
