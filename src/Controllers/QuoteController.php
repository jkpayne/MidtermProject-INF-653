<?php


namespace App\Controllers;

use App\Repositories\AuthorRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\QuoteRepository;
use App\Request;
use App\Response;

final class QuoteController {
    public function __construct(
        private readonly QuoteRepository $quotes = new QuoteRepository(),
        private readonly AuthorRepository $authors = new AuthorRepository(),
        private readonly CategoryRepository $categories = new CategoryRepository(),
    ) {
    }

    public function get(): void {
        $id = Request::queryInt("id");
        $authorId = Request::queryInt("author_id");
        $categoryId = Request::queryInt("category_id");
        $randomParam = Request::query("random");
        $random = $randomParam !== null && strtolower($randomParam) === "true";

        if ($id !== null) {
            $one = $this->quotes->findById($id);
            if ($one === null) {
                Response::json(["message" => "No Quotes Found"]);
                return;
            }
            Response::json($one);
            return;
        }

        if ($authorId !== null || $categoryId !== null || $random) {
            $list = $this->quotes->findFiltered($authorId, $categoryId, $random);
            if ($random) {
                if ($list === []) {
                    Response::json(["message" => "No Quotes Found"]);
                    return;
                }
                Response::json($list[0]);
                return;
            }
            Response::json($list);
            return;
        }

        $all = $this->quotes->all();
        Response::json($all);
    }

    public function post(): void {
        $body = Request::jsonBody();
        $quote = Request::bodyString("quote", $body);
        $authorId = Request::bodyInt("author_id", $body);
        $categoryId = Request::bodyInt("category_id", $body);

        $error = $this->validateRequestData($quote, $authorId, $categoryId);
        if ($error !== null) {
            Response::json(["message" => $error]);
            return;
        }

        Response::json(
            [
                "id" => $this->quotes->create($quote, $authorId, $categoryId),
                "quote" => $quote,
                "author_id" => $authorId,
                "category_id" => $categoryId,
            ],
            201,
        );
    }

    /**
     * Validate quote request data.
     *
     * @param string|null $quote Quote text.
     * @param int|null $authorId Author ID.
     * @param int|null $categoryId Category ID.
     *
     * @returns string|null Error message if invalid, null if valid.
     */
    public function validateRequestData(?string $quote, ?int $authorId, ?int $categoryId): ?string {
        if ($quote === null || $authorId === null || $categoryId === null) {
            return "Missing Required Parameters";
        }
        if (!$this->authors->exists($authorId)) {
            return "author_id Not Found";
        }
        if (!$this->categories->exists($categoryId)) {
            return "category_id Not Found";
        }
        return null;
    }

    public function put(): void {
        $body = Request::jsonBody();
        $id = Request::bodyInt("id", $body);
        $quote = Request::bodyString("quote", $body);
        $authorId = Request::bodyInt("author_id", $body);
        $categoryId = Request::bodyInt("category_id", $body);

        if ($id === null) {
            Response::json(["message" => "Missing Required Parameters"]);
            return;
        }

        $error = $this->validateRequestData($quote, $authorId, $categoryId);
        if (isset($error)) {
            Response::json(["message" => $error]);
            return;
        }

        if (!($this->quotes->update($id, $quote, $authorId, $categoryId))) {
            Response::json(["message" => "No Quotes Found"]);
            return;
        }

        Response::json(
            [
                "id" => $id,
                "quote" => $quote,
                "author_id" => $authorId,
                "category_id" => $categoryId,
            ],
        );
    }

    public function delete(): void {
        $id = Request::deleteId();
        if ($id === null) {
            Response::json(["message" => "Missing Required Parameters"]);
            return;
        }

        $deleted = $this->quotes->delete($id);
        if (!$deleted) {
            Response::json(["message" => "No Quotes Found"]);
            return;
        }

        Response::json(["id" => $id]);
    }
}
