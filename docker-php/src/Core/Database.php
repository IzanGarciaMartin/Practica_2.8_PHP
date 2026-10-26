<?php

namespace App\Core;

use PDO;
use PDOStatement;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $host = getenv('DB_HOST') ?: 'db';
            $dbname = getenv('DB_NAME') ?: 'tablon';
            $user = getenv('DB_USER') ?: 'dev';
            $password = getenv('DB_PASSWORD') ?: 'dev';

            $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

            self::$connection = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$connection;
    }

    public static function query(
        string $sql,
        array $params = []
    ): PDOStatement {
        $statement = self::getConnection()->prepare($sql);
        $statement->execute($params);

        return $statement;
    }

    public static function fetchAll(
        string $sql,
        array $params = []
    ): array {
        return self::query($sql, $params)->fetchAll();
    }

    public static function fetchOne(
        string $sql,
        array $params = []
    ): ?array {
        $result = self::query($sql, $params)->fetch();

        return $result === false ? null : $result;
    }
}
