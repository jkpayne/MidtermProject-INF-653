<?php


namespace App\Controllers;

use App\Repositories\CategoryRepository;
use App\Request;
use App\Response;

final class CategoryController {
    public function __construct(
        private readonly CategoryRepository $categories = new CategoryRepository(),
    ) {
    }

    public function get(): void {
        $id = Request::queryInt("id");
        if ($id !== null) {
            $row = $this->categories->findById($id);
            if ($row === null) {
                Response::json(["message" => "category_id Not Found"]);
                return;
            }
            Response::json($row);
            return;
        }

        Response::json($this->categories->all());
    }

    public function post(): void {
        $body = Request::jsonBody();
        $category = Request::bodyString("category", $body);
        if ($category === null) {
            Response::json(["message" => "Missing Required Parameters"]);
            return;
        }

        $newId = $this->categories->create($category);
        Response::json(["id" => $newId, "category" => $category], 201);
    }

    public function put(): void {
        $body = Request::jsonBody();
        $id = Request::bodyInt("id", $body);
        $category = Request::bodyString("category", $body);

        if ($id === null || $category === null) {
            Response::json(["message" => "Missing Required Parameters"]);
            return;
        }

        $updated = $this->categories->update($id, $category);
        if (!$updated) {
            Response::json(["message" => "category_id Not Found"]);
            return;
        }

        Response::json(["id" => $id, "category" => $category]);
    }

    public function delete(): void {
        $id = Request::deleteId();
        if ($id === null) {
            Response::json(["message" => "Missing Required Parameters"]);
            return;
        }

        $deleted = $this->categories->delete($id);
        if (!$deleted) {
            Response::json(["message" => "category_id Not Found"]);
            return;
        }

        Response::json(["id" => $id]);
    }
}
