<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Database;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Database\PostgresConfig;

class PostgresConfigTest extends TestCase
{
    public function test_ssl_defaults_to_enabled(): void
    {
        $config = new PostgresConfig(['database' => 'testdb']);
        $this->assertTrue($config->useSSL());
    }

    public function test_custom_port_assignment(): void
    {
        $config = new PostgresConfig([
            'database' => 'testdb',
            'port' => '6543'
        ]);
        
        $this->assertSame(6543, $config->getPort());
    }
}
