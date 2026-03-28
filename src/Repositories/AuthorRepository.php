<?php


namespace App\Repositories;

use App\Database;

final class AuthorRepository {
    /**
     * Check if an author exists by ID.
     *
     * @param int $id Author ID.
     *
     * @returns bool True if exists, false otherwise.
     */
    public function exists(int $id): bool {
        $stmt = Database::pdo()->prepare("SELECT 1 FROM authors WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Get all authors.
     * @returns array List of rows with keys id, author.
     */
    public function all(): array {
        return Database::pdo()->query("SELECT id, author FROM authors ORDER BY id ASC")->fetchAll();
    }

    /**
     * Find an author by ID.
     *
     * @param int $id Author ID.
     *
     * @returns array|null Row with id, author or null.
     */
    public function findById(int $id): ?array {
        $stmt = Database::pdo()->prepare("SELECT id, author FROM authors WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Create a new author.
     *
     * @param string $author Author name.
     *
     * @returns int New author ID.
     */
    public function create(string $author): int {
        $stmt = Database::pdo()->prepare("INSERT INTO authors (author) VALUES (?) RETURNING id");
        $stmt->execute([$author]);
        $row = $stmt->fetch();
        return (int)($row["id"] ?? 0);
    }

    /**
     * Update an existing author.
     *
     * @param int $id Author ID.
     * @param string $author Author name.
     *
     * @returns bool True if updated, false if not found.
     */
    public function update(int $id, string $author): bool {
        $stmt = Database::pdo()->prepare("UPDATE authors SET author = ? WHERE id = ?");
        $stmt->execute([$author, $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Delete an author by ID.
     *
     * @param int $id Author ID.
     *
     * @returns bool True if deleted, false if not found or has related quotes.
     */
    public function delete(int $id): bool {
        // Check if author has any quotes (FK constraint)
        $stmt = Database::pdo()->prepare("SELECT COUNT(*) FROM quotes WHERE author_id = ?");
        $stmt->execute([$id]);
        if ((int)$stmt->fetchColumn() > 0) {
            return false;
        }

        $stmt = Database::pdo()->prepare("DELETE FROM authors WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
