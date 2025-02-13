<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Database;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Database\MysqlConfig;

class MysqlConfigTest extends TestCase
{
    public function test_default_values(): void
    {
        $config = new MysqlConfig(['database' => 'testdb']);
        
        $this->assertSame('mysql', $config->getDriver());
        $this->assertSame('localhost', $config->getHost());
        $this->assertSame(3306, $config->getPort());
        $this->assertFalse($config->useSSL());
    }

    public function test_ssl_enabled(): void
    {
        $config = new MysqlConfig([
            'database' => 'testdb',
            'ssl' => 'true'
        ]);
        
        $this->assertTrue($config->useSSL());
    }

    public function test_missing_database_throws_error(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MysqlConfig([]);
    }
}
