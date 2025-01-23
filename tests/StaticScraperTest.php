<?php

namespace JobApplicationAutomation\Tests;

use PHPUnit\Framework\TestCase;
use JobApplicationAutomation\Scrapers\StaticScraper;
use Psr\Log\LoggerInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

class StaticScraperTest extends TestCase
{
    private StaticScraper $scraper;

    protected function setUp(): void
    {
        // Mocking the LoggerInterface
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('info')->willReturn(null);
        $logger->method('error')->willReturn(null);

        // Initialize StaticScraper with mocked dependencies
        $this->scraper = new StaticScraper($logger);
    }

    public function testScrapeReturnsJobListings(): void
    {
        // Mocking HTTP response
        $mockHtml = <<<HTML
        <div class="job-listing">
            <div class="job-title">Software Engineer</div>
            <div class="job-description">Develop software solutions.</div>
            <div class="job-skills">PHP, JavaScript</div>
        </div>
        HTML;

        $mockBrowser = $this->createMock(HttpBrowser::class);
        $mockBrowser->method('request')->willReturn($mockHtml);

        // Inject the mocked HttpBrowser into StaticScraper
        $this->scraper->setHttpBrowser($mockBrowser); // Assume the StaticScraper has a setter for this dependency.

        $results = $this->scraper->scrape('https://hiring.cafe/');
        $this->assertCount(1, $results);
        $this->assertEquals('Software Engineer', $results[0]['title']);
        $this->assertEquals('Develop software solutions, expertise, sofware engineering', $results[0]['description']);
        $this->assertEquals('PHP, JavaScript', $results[0]['skills']);
    }

    public function testInvalidUrlThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->scraper->scrape('invalid-url');
    }

    public function testScrapeHandlesMissingFields(): void
    {
        $mockHtml = <<<HTML
        <div class="job-listing">
            <div class="job-title"></div>
        </div>
        HTML;

        $mockBrowser = $this->createMock(HttpBrowser::class);
        $mockBrowser->method('request')->willReturn($mockHtml);

        $this->scraper->setHttpBrowser($mockBrowser);

        $results = $this->scraper->scrape('https://hiring.cafe/');
        $this->assertEquals('', $results[0]['title']);
    }
}
