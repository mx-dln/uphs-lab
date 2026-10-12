<?php

namespace App\Helpers;

use PDO;

abstract class BaseModel
{
    protected static string $table;
    protected static string $primaryKey = 'id';
    protected static array $fillable = [];
    protected static array $searchable = [];

    public static function all(): array
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT * FROM " . static::$table . " ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::connect();
        $filtered = array_intersect_key($data, array_flip(static::$fillable));
        $columns = implode(', ', array_keys($filtered));
        $placeholders = implode(', ', array_fill(0, count($filtered), '?'));
        $stmt = $db->prepare("INSERT INTO " . static::$table . " ({$columns}) VALUES ({$placeholders})");
        $stmt->execute(array_values($filtered));
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $db = Database::connect();
        $filtered = array_intersect_key($data, array_flip(static::$fillable));
        $set = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($filtered)));
        $stmt = $db->prepare("UPDATE " . static::$table . " SET {$set} WHERE " . static::$primaryKey . " = ?");
        $stmt->execute([...array_values($filtered), $id]);
        return $stmt->rowCount() > 0;
    }

    public static function delete(int $id): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public static function where(string $column, $value, string $operator = '='): array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE {$column} {$operator} ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public static function whereFirst(string $column, $value, string $operator = '='): ?array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE {$column} {$operator} ? LIMIT 1");
        $stmt->execute([$value]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function paginate(int $page = 1, int $perPage = 10): array
    {
        $db = Database::connect();
        $offset = ($page - 1) * $perPage;
        $countStmt = $db->query("SELECT COUNT(*) as total FROM " . static::$table);
        $total = $countStmt->fetch()['total'];
        $stmt = $db->prepare("SELECT * FROM " . static::$table . " ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$perPage, $offset]);
        return [
            'data' => $stmt->fetchAll(),
            'total' => (int)$total,
            'page' => $page,
            'perPage' => $perPage,
            'lastPage' => (int)ceil($total / $perPage),
        ];
    }

    public static function count(): int
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT COUNT(*) as total FROM " . static::$table);
        return (int)$stmt->fetch()['total'];
    }

    public static function countWhere(string $column, $value): int
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM " . static::$table . " WHERE {$column} = ?");
        $stmt->execute([$value]);
        return (int)$stmt->fetch()['total'];
    }

    public static function sum(string $column): float
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT SUM({$column}) as total FROM " . static::$table);
        return (float)$stmt->fetch()['total'];
    }

    public static function search(string $term, array $columns = []): array
    {
        $db = Database::connect();
        $searchColumns = !empty($columns) ? $columns : static::$searchable;
        if (empty($searchColumns)) return [];

        $conditions = implode(' OR ', array_map(fn($col) => "{$col} LIKE ?", $searchColumns));
        $params = array_fill(0, count($searchColumns), "%{$term}%");
        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE {$conditions}");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function raw(string $sql, array $params = []): array
    {
        $db = Database::connect();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function rawOne(string $sql, array $params = []): ?array
    {
        $db = Database::connect();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function execute(string $sql, array $params = []): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }
}
