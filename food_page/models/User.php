<?php
declare(strict_types=1);

final class User extends Model
{
    public static function create(array $data): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO users (name, email, password_hash, role, profile_picture) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['password_hash'],
            $data['role'],
            $data['profile_picture'] ?? null,
        ]);

        return (int) self::db()->lastInsertId();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function findByRememberToken(string $hash): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM users WHERE remember_token = ?');
        $stmt->execute([$hash]);
        return $stmt->fetch() ?: null;
    }

    public static function emailExists(string $email, ?int $exceptId = null): bool
    {
        if ($exceptId) {
            $stmt = self::db()->prepare('SELECT id FROM users WHERE email = ? AND id <> ?');
            $stmt->execute([$email, $exceptId]);
        } else {
            $stmt = self::db()->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
        }

        return (bool) $stmt->fetch();
    }

    public static function updateProfile(int $id, array $data): void
    {
        $stmt = self::db()->prepare('UPDATE users SET name = ?, email = ?, profile_picture = COALESCE(?, profile_picture) WHERE id = ?');
        $stmt->execute([$data['name'], $data['email'], $data['profile_picture'] ?? null, $id]);
    }

    public static function updatePassword(int $id, string $hash): void
    {
        $stmt = self::db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([$hash, $id]);
    }

    public static function setRememberToken(int $id, ?string $hash): void
    {
        $stmt = self::db()->prepare('UPDATE users SET remember_token = ? WHERE id = ?');
        $stmt->execute([$hash, $id]);
    }

    public static function members(): array
    {
        return self::db()
            ->query("SELECT id, name, email, profile_picture, created_at FROM users WHERE role = 'member' ORDER BY created_at DESC")
            ->fetchAll();
    }

    public static function deleteMember(int $id): bool
    {
        $stmt = self::db()->prepare("DELETE FROM users WHERE id = ? AND role = 'member'");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public static function countByRole(string $role): int
    {
        $stmt = self::db()->prepare('SELECT COUNT(*) FROM users WHERE role = ?');
        $stmt->execute([$role]);
        return (int) $stmt->fetchColumn();
    }
}

