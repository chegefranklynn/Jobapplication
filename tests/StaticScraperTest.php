<?php

namespace JobApplication\Tests;

use PHPUnit\Framework\TestCase;
use JobApplication\php\StaticScraper;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Psr\Log\LoggerInterface;

class StaticScraperTest extends TestCase
{
    private HttpBrowser $mockClient;
    private LoggerInterface $mockLogger;
    private StaticScraper $scraper;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(HttpBrowser::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockLogger->method('info')->willReturnSelf();
        $this->mockLogger->method('error')->willReturnSelf();
        
        $this->scraper = new StaticScraper($this->mockLogger);
        $this->scraper->setHttpBrowser($this->mockClient);
    }

    public function testScrapeReturnsJobListings()
    {
        $html = <<<HTML
        <div class="job-listing">
            <h3 class="job-title">Senior PHP Developer</h3>
            <div class="job-meta">
                <span class="job-company">Tech Innovators</span>
                <span class="job-location">Remote</span>
                <span class="job-environment">Full-time</span>
            </div>
            <div class="job-description">Looking for experienced PHP developers...</div>
            <div class="job-skills">PHP 8, Symfony, Docker</div>
        </div>
        HTML;

        $this->configureMockClient($html);
        
        $result = $this->scraper->scrape('https://hiring.cafe/');
        
        $this->assertCount(1, $result);
        $this->assertEquals('Senior PHP Developer', $result[0]['title']);
        $this->assertEquals('Tech Innovators', $result[0]['company']);
    }

    public function testScrapeHandlesMissingFields()
    {
        $html = <<<HTML
        <div class="job-listing">
            <h3 class="job-title">UX Designer</h3>
            <div class="job-meta">
                <!-- Missing company and location -->
                <span class="job-environment">Contract</span>
            </div>
            <div class="job-description">User experience design role...</div>
        </div>
        HTML;

        $this->configureMockClient($html);
        
        $result = $this->scraper->scrape('https://hiring.cafe/');
        
        $this->assertEquals('N/A', $result[0]['company']);
        $this->assertEquals('N/A', $result[0]['location']);
        $this->assertEquals('User experience design role...', $result[0]['description']);
    }

    private function configureMockClient(string $html): void
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($html);
        
        $this->mockClient->method('request')
            ->willReturn($crawler);
    }
}