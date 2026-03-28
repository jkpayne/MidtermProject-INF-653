<?php


require dirname(__DIR__) . "/src/bootstrap.php";

use App\Controllers\AuthorController;
use App\Controllers\CategoryController;
use App\Controllers\QuoteController;
use App\Response;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER["REQUEST_METHOD"] ?? "GET";

if ($method === "OPTIONS") {
    http_response_code(200);
    exit;
}

$rawPath = parse_url($_SERVER["REQUEST_URI"] ?? "/", PHP_URL_PATH);
$rawPath = is_string($rawPath) ? $rawPath : "/";
$uriPath = "/" . trim($rawPath, "/");

$base = "/api";
if (str_starts_with($uriPath, $base)) {
    $rest = substr($uriPath, strlen($base));
    $uriPath = "/" . trim($rest, "/");
}

if ($uriPath === "/" || $uriPath === "") {
    Response::json(["message" => "Not Found"], 404);
    exit;
}

$segments = array_values(array_filter(explode("/", trim($uriPath, "/")), static fn($s) => $s !== ""));
$resource = strtolower($segments[0] ?? "");

$quotes = new QuoteController();
$authors = new AuthorController();
$categories = new CategoryController();

try {
    match ($resource) {
        "quotes" => match ($method) {
            "GET" => $quotes->get(),
            "POST" => $quotes->post(),
            "PUT" => $quotes->put(),
            "DELETE" => $quotes->delete(),
            default => Response::json(["message" => "Method Not Allowed"], 405),
        },
        "authors" => match ($method) {
            "GET" => $authors->get(),
            "POST" => $authors->post(),
            "PUT" => $authors->put(),
            "DELETE" => $authors->delete(),
            default => Response::json(["message" => "Method Not Allowed"], 405),
        },
        "categories" => match ($method) {
            "GET" => $categories->get(),
            "POST" => $categories->post(),
            "PUT" => $categories->put(),
            "DELETE" => $categories->delete(),
            default => Response::json(["message" => "Method Not Allowed"], 405),
        },
        default => Response::json(["message" => "Not Found"], 404),
    };
} catch (Throwable) {
    Response::json(["message" => "Server Error"], 500);
}
exit;
