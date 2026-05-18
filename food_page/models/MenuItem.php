<?php
declare(strict_types=1);

final class MenuItem extends Model
{
    public static function byRestaurant(int $restaurantId): array
    {
        $stmt = self::db()->prepare('SELECT * FROM menu_items WHERE restaurant_id = ? ORDER BY created_at DESC');
        $stmt->execute([$restaurantId]);
        return $stmt->fetchAll();
    }

    public static function allWithRestaurant(): array
    {
        return self::db()
            ->query('SELECT mi.*, r.name AS restaurant_name FROM menu_items mi JOIN restaurants r ON r.id = mi.restaurant_id ORDER BY mi.created_at DESC')
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare(
            'SELECT mi.*, r.name AS restaurant_name, r.location, r.area FROM menu_items mi JOIN restaurants r ON r.id = mi.restaurant_id WHERE mi.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare('INSERT INTO menu_items (restaurant_id, name, description, price, image_path) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$data['restaurant_id'], $data['name'], $data['description'], $data['price'], $data['image_path'] ?? null]);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = self::db()->prepare('UPDATE menu_items SET name = ?, description = ?, price = ?, image_path = COALESCE(?, image_path) WHERE id = ?');
        $stmt->execute([$data['name'], $data['description'], $data['price'], $data['image_path'] ?? null, $id]);
    }

    public static function delete(int $id): void
    {
        $stmt = self::db()->prepare('DELETE FROM menu_items WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function search(array $filters): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(r.name LIKE ? OR mi.name LIKE ? OR mi.description LIKE ?)';
            $needle = '%' . $filters['q'] . '%';
            array_push($params, $needle, $needle, $needle);
        }

        if (!empty($filters['location'])) {
            $where[] = 'r.location = ?';
            $params[] = $filters['location'];
        }

        if (!empty($filters['area'])) {
            $where[] = 'r.area = ?';
            $params[] = $filters['area'];
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $where[] = 'mi.price >= ?';
            $params[] = (float) $filters['min_price'];
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $where[] = 'mi.price <= ?';
            $params[] = (float) $filters['max_price'];
        }

        $sql = 'SELECT mi.*, r.name AS restaurant_name, r.location, r.area FROM menu_items mi JOIN restaurants r ON r.id = mi.restaurant_id';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY mi.created_at DESC LIMIT 50';

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function count(): int
    {
        return (int) self::db()->query('SELECT COUNT(*) FROM menu_items')->fetchColumn();
    }
}
