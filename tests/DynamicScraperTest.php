<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use JobApplicationAutomation\Scrapers\DynamicScraper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DynamicScraperTest extends TestCase
{
    private DynamicsScraper $scraper;

    protected function setUp(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->scraper = new DynamicScraper('/src/node/puppeteerScript.js', 5, $logger);
    }

    public function testScrapeReturnsJobListings(): void
    {
        $mockOutput = json_encode([['title' => 'Software Engineer', 'description' => 'software engineering', 'skills' => 'PostgreSQL, SQL, PHP, AWS, Azure']]);

        $mockProcess = $this->createMock(Process::class);
        $mockProcess->method('run')->willReturn(true);
        $mockProcess->method('getOutput')->willReturn($mockOutput);

        $this->scraper->setProcess($mockProcess);
        $results = $this->scraper->scrape('https://hiring.cafe/');

        $this->assertCount(1, $results);
        $this->assertEquals('Software Engineer', $results[0]['title']);
    }

    public function testScrapeThrowsExceptionOnProcessFailure(): void
    {
        $this->expectException(ProcessFailedException::class);

        $mockProcess = $this->createMock(Process::class);
        $mockProcess->method('run')->willThrowException(new ProcessFailedException(new Process([])));

        $this->scraper->setProcess($mockProcess);
        $this->scraper->scrape('https://hiring.cafe/');
    }

    public function testScrapeThrowsExceptionOnMalformedJson(): void
    {
        $this->expectException(\RuntimeException::class);

        $mockProcess = $this->createMock(Process::class);
        $mockProcess->method('run')->willReturn(true);
        $mockProcess->method('getOutput')->willReturn('{invalid_json');

        $this->scraper->setProcess($mockProcess);
        $this->scraper->scrape('https://hiring.cafe/');
    }
}
