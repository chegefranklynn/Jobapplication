<?php

namespace JobApplication\php;

use JobApplication\php\StaticScraper;
use JobApplication\php\DynamicScraper;
use JobApplication\php\ScraperInterface;
use InvalidArgumentException;

class ScraperFactory
{
    public function create(string $type, string $url): ScraperInterface
    {
        // Validate URL first
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("Invalid URL format: $url");
        }

        return match(strtolower($type)) {
            'static' => new StaticScraper(),
            'dynamic' => new DynamicScraper(),
            default => throw new \InvalidArgumentException("Invalid scraper type: $type")
        };
    }
}