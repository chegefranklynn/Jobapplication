<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;
use PDOException;
use Psr\Log\LoggerInterface;

final class SQLConnection
{
    private ?PDO $connection = null;
    
    public function __construct(
        private readonly SQLConfig $config,
        private readonly ?LoggerInterface $logger = null
    ) {}
    
    public function connect(): PDO
    {
        if ($this->connection === null) {
            $this->establishConnection();
        }
        
        return $this->connection;
    }
    
    private function establishConnection(): void
    {
        $driver = $this->config->getDriver();
        $dsn = $this->buildDSN();
        
        try {
            $this->connection = new PDO(
                $dsn,
                $this->config->getUser(),
                $this->config->getPassword(),
                $this->getDriverOptions()
            );
            
            $this->logger?->info("Connected to {$driver} database", [
                'host' => $this->config->getHost(),
                'database' => $this->config->getDatabase()
            ]);
            
        } catch (PDOException $e) {
            $this->logger?->critical("Database connection failed: " . $e->getMessage(), [
                'driver' => $driver,
                'errorCode' => $e->getCode()
            ]);
            throw new DatabaseConnectionException("Connection to {$driver} failed", 0, $e);
        }
    }
    
    private function buildDSN(): string
    {
        $driver = $this->config->getDriver();
        $params = [
            'host' => $this->config->getHost(),
            'port' => $this->config->getPort(),
            'dbname' => $this->config->getDatabase(),
            'charset' => $this->config->getCharset()
        ];
        
        if ($driver === 'pgsql') {
            $params['sslmode'] = $this->config->useSSL() ? 'require' : 'prefer';
        }
        
        return $driver . ':' . http_build_query($params, '', ';');
    }
    
    private function getDriverOptions(): array
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => $this->config->getTimeout(),
            PDO::ATTR_PERSISTENT => false
        ];
        
        if ($this->config->getDriver() === 'mysql') {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES '{$this->config->getCharset()}'";
            $options[PDO::MYSQL_ATTR_SSL_CA] = $this->config->useSSL() 
                ? '/etc/ssl/certs/ca-certificates.crt' 
                : null;
        }
        
        return $options;
    }
    
    public function isConnected(): bool
    {
        try {
            $this->connect()->query('SELECT 1');
            return true;
        } catch (PDOException) {
            return false;
        }
    }
}