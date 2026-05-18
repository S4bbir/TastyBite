<?php
declare(strict_types=1);

final class FoodExperience extends Model
{
    public static function posts(): array
    {
        return self::db()
            ->query('SELECT fep.*, users.name AS author, restaurants.name AS restaurant_name, menu_items.name AS menu_item_name FROM food_experience_posts fep JOIN users ON users.id = fep.user_id LEFT JOIN restaurants ON restaurants.id = fep.restaurant_id LEFT JOIN menu_items ON menu_items.id = fep.menu_item_id ORDER BY fep.created_at DESC')
            ->fetchAll();
    }

    public static function findPost(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM food_experience_posts WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function createPost(array $data): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO food_experience_posts (user_id, title, content, post_type, restaurant_id, menu_item_id) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['user_id'],
            $data['title'],
            $data['content'],
            $data['post_type'],
            $data['restaurant_id'] ?: null,
            $data['menu_item_id'] ?: null,
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function updatePost(int $id, array $data): void
    {
        $stmt = self::db()->prepare(
            'UPDATE food_experience_posts SET title = ?, content = ?, post_type = ?, restaurant_id = ?, menu_item_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?'
        );
        $stmt->execute([
            $data['title'],
            $data['content'],
            $data['post_type'],
            $data['restaurant_id'] ?: null,
            $data['menu_item_id'] ?: null,
            $id,
        ]);
    }

    public static function deletePost(int $id): void
    {
        $stmt = self::db()->prepare('DELETE FROM food_experience_posts WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function commentsByPost(int $postId): array
    {
        $stmt = self::db()->prepare(
            'SELECT fec.*, users.name AS author FROM food_experience_comments fec JOIN users ON users.id = fec.user_id WHERE fec.post_id = ? ORDER BY fec.created_at ASC'
        );
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }

    public static function createComment(int $postId, int $userId, string $comment): int
    {
        $stmt = self::db()->prepare('INSERT INTO food_experience_comments (post_id, user_id, comment) VALUES (?, ?, ?)');
        $stmt->execute([$postId, $userId, $comment]);
        return (int) self::db()->lastInsertId();
    }

    public static function findComment(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM food_experience_comments WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function deleteComment(int $id): void
    {
        $stmt = self::db()->prepare('DELETE FROM food_experience_comments WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function allCommentsWithContext(): array
    {
        return self::db()
            ->query('SELECT fec.*, users.name AS author, fep.title AS post_title FROM food_experience_comments fec JOIN users ON users.id = fec.user_id JOIN food_experience_posts fep ON fep.id = fec.post_id ORDER BY fec.created_at DESC')
            ->fetchAll();
    }

    public static function countPosts(): int
    {
        return (int) self::db()->query('SELECT COUNT(*) FROM food_experience_posts')->fetchColumn();
    }
}

