<?php


namespace App;

use JsonException;

/**
 * JSON HTTP responses with a consistent Content-Type.
 */
final class Response {
    /**
     * @param mixed $data Data to encode as JSON. Can be any value supported by json_encode().
     * @param int $status HTTP status code (default 200). Should be a valid HTTP status code.
     *
     * @returns void Outputs JSON response and sets appropriate headers. Does not return a value.
     */
    public static function json(mixed $data, int $status = 200): void {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
        http_response_code($status);
        header("Content-Type: application/json;");
        try {
            echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (JsonException $e) {
            echo sprintf(
                '{"error": "JSON encoding failed: %s"}',
                htmlspecialchars($e->getMessage(), ENT_QUOTES, "UTF-8"),
            );
        }
    }
}
