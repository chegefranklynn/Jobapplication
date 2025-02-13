<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

interface SQLConfig
{
    public function getDriver(): string; // mysql or pgsql
    public function getHost(): string;
    public function getPort(): int;
    public function getDatabase(): string;
    public function getUser(): string;
    public function getPassword(): string;
    public function useSSL(): bool;
    public function getCharset(): string;
    public function getTimeout(): int; // Connection timeout in seconds
}