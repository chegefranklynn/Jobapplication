<?php

use PHPUnit\Framework\TestCase;
use JobApplicationAutomation\Factory\ScraperFactory;
use JobApplicationAutomation\Scrapers\StaticScraper;
use JobApplicationAutomation\Scrapers\DynamicScraper;
use InvalidArgumentException;

class ScraperFactoryTest extends TestCase
{
    public function testCreateStaticScraper(): void
    {
        $scraper = ScraperFactory::createScraper('static');
        $this->assertInstanceOf(StaticScraper::class, $scraper);
    }

    public function testCreateDynamicScraper(): void
    {
        $scraper = ScraperFactory::createScraper('dynamic');
        $this->assertInstanceOf(DynamicScraper::class, $scraper);
    }

    public function testCreateUnknownScraperThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ScraperFactory::createScraper('unknown');
    }

    public function testCreateInvalidScraperTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ScraperFactory::createScraper('');
        ScraperFactory::createScraper(null);
        ScraperFactory::createScraper(123);
    }
}
