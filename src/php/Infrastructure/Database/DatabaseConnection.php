<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;
use PDOException;

class DatabaseConnection
{
    private static ?PDO $instance = null;
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY_MS = 100;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                '%s:host=%s;port=%d;dbname=%s;sslmode=require',
                $_ENV['DB_DRIVER'] ?? 'pgsql', // Added driver flexibility
                $_ENV['DB_HOST'],
                $_ENV['DB_PORT'],
                $_ENV['DB_NAME']
            );

            self::$instance = new PDO(
                $dsn,
                $_ENV['DB_USER'],
                $_ENV['DB_PASS'],
                self::getDriverOptions()
            );
        }
        return self::$instance;
    }

    private static function getDriverOptions(): array
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => true
        ];

        // Driver-specific options
        if ($_ENV['DB_DRIVER'] === 'pgsql') {
            $options[PDO::PGSQL_ATTR_DISABLE_PREPARES] = false;
        } elseif ($_ENV['DB_DRIVER'] === 'mysql') {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $_ENV['DB_SSL_CA'] ?? '';
        }

        return $options;
    }

    public function transactional(callable $callback): mixed {
        $pdo = self::getInstance();
        
        try {
            $pdo->beginTransaction();
            $result = $callback($pdo);
            $pdo->commit();
            return $result;
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
