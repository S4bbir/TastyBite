<?php
declare(strict_types=1);

final class RestaurantReview extends Model
{
    public static function byRestaurant(int $restaurantId): array
    {
        $stmt = self::db()->prepare(
            'SELECT rr.*, users.name AS author FROM restaurant_reviews rr JOIN users ON users.id = rr.user_id WHERE rr.restaurant_id = ? ORDER BY rr.created_at DESC'
        );
        $stmt->execute([$restaurantId]);
        return $stmt->fetchAll();
    }

    public static function create(int $restaurantId, int $userId, int $rating, string $comment): int
    {
        $stmt = self::db()->prepare('INSERT INTO restaurant_reviews (restaurant_id, user_id, rating, comment) VALUES (?, ?, ?, ?)');
        $stmt->execute([$restaurantId, $userId, $rating, $comment]);
        return (int) self::db()->lastInsertId();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM restaurant_reviews WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function delete(int $id): void
    {
        $stmt = self::db()->prepare('DELETE FROM restaurant_reviews WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function allWithContext(): array
    {
        return self::db()
            ->query('SELECT rr.*, users.name AS author, restaurants.name AS restaurant_name FROM restaurant_reviews rr JOIN users ON users.id = rr.user_id JOIN restaurants ON restaurants.id = rr.restaurant_id ORDER BY rr.created_at DESC')
            ->fetchAll();
    }
}

