<?php
declare(strict_types=1);

final class Review extends Model
{
    public static function byMenuItem(int $menuItemId): array
    {
        $stmt = self::db()->prepare(
            'SELECT reviews.*, users.name AS author FROM reviews JOIN users ON users.id = reviews.user_id WHERE menu_item_id = ? ORDER BY reviews.created_at DESC'
        );
        $stmt->execute([$menuItemId]);
        return $stmt->fetchAll();
    }

    public static function create(int $menuItemId, int $userId, string $comment): int
    {
        $stmt = self::db()->prepare('INSERT INTO reviews (menu_item_id, user_id, comment) VALUES (?, ?, ?)');
        $stmt->execute([$menuItemId, $userId, $comment]);
        return (int) self::db()->lastInsertId();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM reviews WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function delete(int $id): void
    {
        $stmt = self::db()->prepare('DELETE FROM reviews WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function allWithContext(): array
    {
        return self::db()
            ->query('SELECT reviews.*, users.name AS author, menu_items.name AS item_name FROM reviews JOIN users ON users.id = reviews.user_id JOIN menu_items ON menu_items.id = reviews.menu_item_id ORDER BY reviews.created_at DESC')
            ->fetchAll();
    }

    public static function count(): int
    {
        return (int) self::db()->query('SELECT COUNT(*) FROM reviews')->fetchColumn();
    }
}

