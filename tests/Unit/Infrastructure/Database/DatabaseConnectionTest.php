<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Database;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Database\DatabaseConnection;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

class DatabaseConnectionTest extends TestCase
{
    private array $config;
    private TestHandler $logHandler;
    
    protected function setUp(): void
    {
        $this->config = [
            'driver' => 'sqlite',
            'host' => 'localhost',
            'port' => 0,
            'database' => ':memory:',
            'user' => 'root',
            'password' => '',
            'charset' => 'utf8'
        ];

        $this->logHandler = new TestHandler();
        $logger = new Logger('test');
        $logger->pushHandler($this->logHandler);
        
        $this->db = DatabaseConnection::getInstance($this->config, $logger);
    }
    
    public function test_sqlite_in_memory_connection(): void
    {
        $pdo = $this->db->getConnection();
        $this->assertInstanceOf(\PDO::class, $pdo);
        
        // Test basic query execution
        $pdo->exec('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $pdo->exec('INSERT INTO test (name) VALUES ("test")');
        
        $result = $pdo->query('SELECT * FROM test')->fetchAll();
        $this->assertCount(1, $result);
    }

    public function test_invalid_config_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $badConfig = $this->config;
        unset($badConfig['driver']);
        
        new DatabaseConnection($badConfig, $this->createMock(Logger::class));
    }
}
