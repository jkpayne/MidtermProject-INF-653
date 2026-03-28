<?php

namespace App\Controllers;

use App\Repositories\AuthorRepository;
use App\Request;
use App\Response;

final class AuthorController {
    public function __construct(
        private readonly AuthorRepository $authors = new AuthorRepository(),
    ) {
    }

    public function get(): void {
        $id = Request::queryInt("id");
        if ($id !== null) {
            $row = $this->authors->findById($id);
            if ($row === null) {
                Response::json(["message" => "author_id Not Found"]);
                return;
            }
            Response::json($row);
            return;
        }

        Response::json($this->authors->all());
    }

    public function post(): void {
        $author = Request::bodyString("author", Request::jsonBody());
        if ($author === null) {
            Response::json(["message" => "Missing Required Parameters"]);
            return;
        }

        $newId = $this->authors->create($author);
        Response::json(["id" => $newId, "author" => $author], 201);
    }

    public function put(): void {
        $body = Request::jsonBody();
        $id = Request::bodyInt("id", $body);
        $author = Request::bodyString("author", $body);

        if ($id === null || $author === null) {
            Response::json(["message" => "Missing Required Parameters"]);
            return;
        }

        $updated = $this->authors->update($id, $author);
        if (!$updated) {
            Response::json(["message" => "author_id Not Found"]);
            return;
        }

        Response::json(["id" => $id, "author" => $author]);
    }

    public function delete(): void {
        $id = Request::deleteId();
        if ($id === null) {
            Response::json(["message" => "Missing Required Parameters"]);
            return;
        }

        $deleted = $this->authors->delete($id);
        if (!$deleted) {
            Response::json(["message" => "author_id Not Found"]);
            return;
        }

        Response::json(["id" => $id]);
    }
}
