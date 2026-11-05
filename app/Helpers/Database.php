<?php

namespace App\Helpers;

use PDO;
use PDOException;
use Exception;

class Database
{
    protected static ?PDO $instance = null;
    protected static string $driver = 'sqlite';

    public static function getConnection(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $config = require dirname(__DIR__, 2) . '/config/database.php';
        $default = $config['default'] ?? 'sqlite';
        self::$driver = $default;
        $connConfig = $config['connections'][$default] ?? [];

        try {
            if ($default === 'mysql') {
                $dsn = sprintf(
                    "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                    $connConfig['host'] ?? '127.0.0.1',
                    $connConfig['port'] ?? '3306',
                    $connConfig['database'] ?? 'insightflow',
                    $connConfig['charset'] ?? 'utf8mb4'
                );
                self::$instance = new PDO(
                    $dsn,
                    $connConfig['username'] ?? 'root',
                    $connConfig['password'] ?? '',
                    $connConfig['options'] ?? []
                );
            } elseif ($default === 'sqlite') {
                $dbPath = $connConfig['database'] ?? dirname(__DIR__, 2) . '/storage/database/insightflow.sqlite';
                $dir = dirname($dbPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $dsn = "sqlite:" . $dbPath;
                self::$instance = new PDO($dsn, null, null, $connConfig['options'] ?? []);
                // Enable foreign keys in SQLite
                self::$instance->exec('PRAGMA foreign_keys = ON;');
            } else {
                throw new Exception("Unsupported database driver: {$default}");
            }
        } catch (PDOException $e) {
            // If MySQL failed and driver was mysql, we can log and throw meaningful message
            error_log("Database connection failed [{$default}]: " . $e->getMessage());
            throw new Exception("Database connection error. Please verify your database configuration.");
        }

        return self::$instance;
    }

    public static function getDriver(): string
    {
        return self::$driver;
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }

    public static function fetchColumn(string $sql, array $params = [], int $column = 0): mixed
    {
        return self::query($sql, $params)->fetchColumn($column);
    }

    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }

    public static function lastInsertId(): string|int
    {
        return self::getConnection()->lastInsertId();
    }

    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    public static function rollBack(): bool
    {
        return self::getConnection()->rollBack();
    }

    /**
     * Helper to return SQL date format expression depending on current DB driver
     */
    public static function dateFormat(string $column, string $format = 'YYYY-MM'): string
    {
        if (self::$driver === 'sqlite') {
            return match ($format) {
                'YYYY-MM' => "strftime('%Y-%m', {$column})",
                'YYYY-MM-DD' => "strftime('%Y-%m-%d', {$column})",
                'YYYY' => "strftime('%Y', {$column})",
                default => "strftime('%Y-%m', {$column})"
            };
        } else {
            return match ($format) {
                'YYYY-MM' => "DATE_FORMAT({$column}, '%Y-%m')",
                'YYYY-MM-DD' => "DATE_FORMAT({$column}, '%Y-%m-%d')",
                'YYYY' => "DATE_FORMAT({$column}, '%Y')",
                default => "DATE_FORMAT({$column}, '%Y-%m')"
            };
        }
    }
}

