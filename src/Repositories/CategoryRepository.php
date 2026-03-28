<?php


namespace App\Repositories;

use App\Database;

final class CategoryRepository {
    /**
     * Check if a category exists by ID.
     *
     * @param int $id Category ID.
     *
     * @returns bool True if exists, false otherwise.
     */
    public function exists(int $id): bool {
        $stmt = Database::pdo()->prepare("SELECT 1 FROM categories WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Get all categories.
     * @returns array List of rows with keys id, category.
     */
    public function all(): array {
        $stmt = Database::pdo()->query("SELECT id, category FROM categories ORDER BY id");
        return $stmt->fetchAll();
    }

    /**
     * Find a category by ID.
     *
     * @param int $id Category ID.
     *
     * @returns array|null Row with id, category or null.
     */
    public function findById(int $id): ?array {
        $stmt = Database::pdo()->prepare("SELECT id, category FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Create a new category.
     *
     * @param string $category Category name.
     *
     * @returns int New category ID.
     */
    public function create(string $category): int {
        $stmt = Database::pdo()->prepare("INSERT INTO categories (category) VALUES (?) RETURNING id");
        $stmt->execute([$category]);
        $row = $stmt->fetch();
        return (int)($row["id"] ?? 0);
    }

    /**
     * Update an existing category.
     *
     * @param int $id Category ID.
     * @param string $category Category name.
     *
     * @returns bool True if updated, false if not found.
     */
    public function update(int $id, string $category): bool {
        $stmt = Database::pdo()->prepare("UPDATE categories SET category = ? WHERE id = ?");
        $stmt->execute([$category, $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Delete a category by ID.
     *
     * @param int $id Category ID.
     *
     * @returns bool True if deleted, false if not found.
     */
    public function delete(int $id): bool {
        $stmt = Database::pdo()->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
