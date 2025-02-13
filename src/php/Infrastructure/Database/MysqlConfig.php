<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

final class MysqlConfig implements SQLConfig
{
    public function __construct(
        private readonly array $config
    ) {}
    
    public function getDriver(): string { return 'mysql'; }
    
    public function getHost(): string { 
        return $this->config['host'] ?? 'localhost'; 
    }
    
    public function getPort(): int { 
        return (int)($this->config['port'] ?? 3306); 
    }
    
    public function getDatabase(): string { 
        return $this->config['database']; 
    }
    
    public function getUser(): string { 
        return $this->config['user'] ?? 'root'; 
    }
    
    public function getPassword(): string { 
        return $this->config['password'] ?? ''; 
    }
    
    public function useSSL(): bool { 
        return filter_var($this->config['ssl'] ?? false, FILTER_VALIDATE_BOOL); 
    }
    
    public function getCharset(): string { 
        return $this->config['charset'] ?? 'utf8mb4'; 
    }
    
    public function getTimeout(): int { 
        return (int)($this->config['timeout'] ?? 5); 
    }
}