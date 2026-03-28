<?php


namespace App;

use JsonException;

/**
 * Normalized access to query params and JSON bodies.
 */
final class Request {
    /**
     * Extract a string field from JSON body.
     *
     * @param string $name Field from JSON body.
     * @param array|null $body Decoded body array.
     *
     * @returns string|null Trimmed string or null if missing/empty.
     */
    public static function bodyString(string $name, ?array $body): ?string {
        if ($body === null || !array_key_exists($name, $body)) {
            return null;
        }

        $v = $body[$name];
        if ($v === null) {
            return null;
        }

        $trimmed = trim((string)$v);
        return $trimmed === "" ? null : $trimmed;
    }

    /**
     * Get resource ID from query string or JSON body (for DELETE).
     * @returns int|null Quote/author/category id from query string or JSON body.
     */
    public static function deleteId(): ?int {
        return self::queryInt("id") ?? self::bodyInt("id", self::jsonBody());
    }

    /**
     * @param string $name Query parameter name.
     *
     * @returns int|null integer or null if missing/invalid.
     */
    public static function queryInt(string $name): ?int {
        $q = self::query($name);
        if ($q === null || $q === "") {
            return null;
        }

        if (!ctype_digit($q)) {
            return null;
        }

        return (int)$q;
    }

    /**
     * @param string $name Query parameter name.
     *
     * @returns string|null String value or null if missing.
     */
    public static function query(string $name): ?string {
        if (!isset($_GET[$name])) {
            return null;
        }

        $v = $_GET[$name];
        if (is_array($v)) {
            return null;
        }

        return (string)$v;
    }

    /**
     * Extract an integer field from JSON body.
     *
     * @param string $name Field from JSON body.
     * @param array|null $body Decoded body array.
     *
     * @returns int|null Positive integer or null if missing/invalid.
     */
    public static function bodyInt(string $name, ?array $body): ?int {
        if ($body === null || !array_key_exists($name, $body)) {
            return null;
        }

        $v = $body[$name];
        if ($v === null || $v === "") {
            return null;
        }

        if (is_int($v)) {
            return $v > 0 ? $v : null;
        }

        if (is_string($v) && ctype_digit($v)) {
            $n = (int)$v;
            return $n > 0 ? $n : null;
        }

        return null;
    }

    /**
     * @returns array|null Decoded JSON object as associative array, or null if body is empty/invalid.
     */
    public static function jsonBody(): ?array {
        $raw = file_get_contents("php://input");
        if ($raw === false || trim($raw) === "") {
            return null;
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }
        return is_array($decoded) ? $decoded : null;
    }
}
