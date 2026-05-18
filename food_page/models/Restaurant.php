<?php
declare(strict_types=1);

final class Restaurant extends Model
{
    public static function all(): array
    {
        return self::db()
            ->query('SELECT r.*, (SELECT COUNT(*) FROM menu_items mi WHERE mi.restaurant_id = r.id) AS menu_count FROM restaurants r ORDER BY r.created_at DESC')
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM restaurants WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare('INSERT INTO restaurants (name, location, area, short_background, goals) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$data['name'], $data['location'], $data['area'], $data['short_background'], $data['goals']]);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = self::db()->prepare('UPDATE restaurants SET name = ?, location = ?, area = ?, short_background = ?, goals = ? WHERE id = ?');
        $stmt->execute([$data['name'], $data['location'], $data['area'], $data['short_background'], $data['goals'], $id]);
    }

    public static function delete(int $id): void
    {
        $stmt = self::db()->prepare('DELETE FROM restaurants WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function locations(): array
    {
        return self::db()->query('SELECT DISTINCT location FROM restaurants ORDER BY location')->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function areas(): array
    {
        return self::db()->query('SELECT DISTINCT area FROM restaurants ORDER BY area')->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function search(array $filters): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(r.name LIKE ? OR r.short_background LIKE ? OR r.goals LIKE ? OR mi.name LIKE ? OR mi.description LIKE ?)';
            $needle = '%' . $filters['q'] . '%';
            array_push($params, $needle, $needle, $needle, $needle, $needle);
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

        $sql = 'SELECT DISTINCT r.*, (SELECT COUNT(*) FROM menu_items mic WHERE mic.restaurant_id = r.id) AS menu_count FROM restaurants r LEFT JOIN menu_items mi ON mi.restaurant_id = r.id';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY r.created_at DESC LIMIT 50';

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function count(): int
    {
        return (int) self::db()->query('SELECT COUNT(*) FROM restaurants')->fetchColumn();
    }
}
