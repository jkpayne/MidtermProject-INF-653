<?php


namespace App\Repositories;

use App\Database;

final class QuoteRepository {
    /**
     * Get all quotes with author and category names.
     * @returns array List of public quote rows.
     */
    public function all(): array {
        $sql = "SELECT q.id, q.quote, a.author AS author, c.category AS category
                FROM quotes q
                INNER JOIN authors a ON a.id = q.author_id
                INNER JOIN categories c ON c.id = q.category_id
                ORDER BY q.id";
        $stmt = Database::pdo()->query($sql);
        $rows = $stmt->fetchAll();
        return array_map(fn(array $r) => $this->mapPublic($r), $rows);
    }

    /**
     * Map DB row to API shape with author and category names.
     *
     * @param array $row Keys id, quote, author, category (joined names).
     *
     * @returns array Keys id, quote, author, category.
     */
    private function mapPublic(array $row): array {
        return [
            "id" => (int)$row["id"],
            "quote" => $row["quote"],
            "author" => $row["author"],
            "category" => $row["category"],
        ];
    }

    /**
     * Find a single quote by ID with author and category names.
     *
     * @param int $id Quote ID.
     *
     * @returns array|null Public quote row or null.
     */
    public function findById(int $id): ?array {
        $sql = "SELECT q.id, q.quote, a.author AS author, c.category AS category
                FROM quotes q
                INNER JOIN authors a ON a.id = q.author_id
                INNER JOIN categories c ON c.id = q.category_id
                WHERE q.id = ?";
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $this->mapPublic($row);
    }

    /**
     * Find quotes with optional filters for author and/or category.
     *
     * @param int|null $authorId Optional author filter.
     * @param int|null $categoryId Optional category filter.
     * @param bool $random When true, return at most one random row.
     *
     * @returns array List of public quote rows (empty, one, or many).
     */
    public function findFiltered(?int $authorId, ?int $categoryId, bool $random): array {
        $sql = "SELECT q.id, q.quote, a.author AS author, c.category AS category
                FROM quotes q
                INNER JOIN authors a ON a.id = q.author_id
                INNER JOIN categories c ON c.id = q.category_id
                WHERE 1=1"; // Need a dummy condition for easier appending of AND clauses.
        $params = [];

        if (isset($authorId)) {
            $sql .= " AND q.author_id = ?";
            $params[] = $authorId;
        }
        if (isset($categoryId)) {
            $sql .= " AND q.category_id = ?";
            $params[] = $categoryId;
        }

        if ($random) {
            $sql .= " ORDER BY RANDOM() LIMIT 1";
        } else {
            $sql .= " ORDER BY q.id ASC";
        }

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        return array_map(fn(array $r) => $this->mapPublic($r), $rows);
    }

    /**
     * Create a new quote.
     *
     * @param string $quote The quote text.
     * @param int $authorId Author ID.
     * @param int $categoryId Category ID.
     *
     * @returns int New quote ID.
     */
    public function create(string $quote, int $authorId, int $categoryId): int {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO quotes (quote, author_id, category_id) VALUES (?, ?, ?) RETURNING id",
        );
        $stmt->execute([$quote, $authorId, $categoryId]);
        $row = $stmt->fetch();
        return (int)($row["id"] ?? 0);
    }

    /**
     * Update an existing quote.
     *
     * @param int $id Quote ID.
     * @param string $quote The quote text.
     * @param int $authorId Author ID.
     * @param int $categoryId Category ID.
     *
     * @returns bool True if updated, false if not found.
     */
    public function update(int $id, string $quote, int $authorId, int $categoryId): bool {
        $stmt = Database::pdo()->prepare(
            "UPDATE quotes SET quote = ?, author_id = ?, category_id = ? WHERE id = ?",
        );
        $stmt->execute([$quote, $authorId, $categoryId, $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Delete a quote by ID.
     *
     * @param int $id Quote ID.
     *
     * @returns bool True if deleted, false if not found.
     */
    public function delete(int $id): bool {
        $stmt = Database::pdo()->prepare("DELETE FROM quotes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
