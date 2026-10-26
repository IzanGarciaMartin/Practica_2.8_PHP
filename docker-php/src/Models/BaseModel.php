<?php

namespace App\Models;

use App\Core\Database;
use PDOStatement;

abstract class BaseModel
{
    protected string $table;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    protected function query(
        string $sql,
        array $params = []
    ): PDOStatement {
        return Database::query($sql, $params);
    }

    protected function fetchAll(
        string $sql,
        array $params = []
    ): array {
        return Database::fetchAll($sql, $params);
    }

    protected function fetchOne(
        string $sql,
        array $params = []
    ): ?array {
        return Database::fetchOne($sql, $params);
    }

    public function getAll(): array
    {
        return $this->fetchAll(
            "SELECT * FROM {$this->table}"
        );
    }

    public function getById(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT * FROM {$this->table} WHERE id = ?",
            [$id]
        );
    }

    public function delete(int $id): bool
    {
        $statement = $this->query(
            "DELETE FROM {$this->table} WHERE id = ?",
            [$id]
        );

        return $statement->rowCount() > 0;
    }

    public function insert(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $this->query($sql, array_values($data));

        return (int) Database::getConnection()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        $fields = [];

        foreach (array_keys($data) as $column) {
            $fields[] = "{$column} = ?";
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE id = ?',
            $this->table,
            implode(', ', $fields)
        );

        $params = array_values($data);
        $params[] = $id;

        $statement = $this->query($sql, $params);

        return $statement->rowCount() > 0;
    }
}
