<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Database;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Database\SQLConnection;
use App\Infrastructure\Database\MysqlConfig;
use Psr\Log\Test\TestLogger;

class SQLConnectionTest extends TestCase
{
    private TestLogger $logger;
    
    protected function setUp(): void
    {
        $this->logger = new TestLogger();
    }

    public function test_successful_connection(): void
    {
        $config = new MysqlConfig([
            'host' => $_ENV['TEST_MYSQL_HOST'] ?? 'localhost',
            'database' => 'testdb',
            'user' => 'root',
            'password' => ''
        ]);
        
        $connection = new SQLConnection($config, $this->logger);
        $pdo = $connection->connect();
        
        $this->assertTrue($connection->isConnected());
        $this->assertTrue($this->logger->hasInfoThatContains('Connected'));
    }

    public function test_invalid_credentials(): void
    {
        $config = new MysqlConfig([
            'database' => 'testdb',
            'user' => 'wrong',
            'password' => 'badpass'
        ]);
        
        $connection = new SQLConnection($config, $this->logger);
        
        $this->expectException(\PDOException::class);
        $connection->connect();
        
        $this->assertTrue($this->logger->hasCriticalRecords());
    }

    public function test_connection_timeout(): void
    {
        $config = new MysqlConfig([
            'host' => '10.255.255.1', // Unreachable IP
            'database' => 'testdb',
            'timeout' => 1
        ]);
        
        $connection = new SQLConnection($config, $this->logger);
        
        $this->expectException(\PDOException::class);
        $connection->connect();
    }
}
