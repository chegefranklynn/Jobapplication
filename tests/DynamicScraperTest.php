<?php 
namespace Tests;

use PHPUnit\Framework\TestCase;
use JobApplication\php\DynamicScraper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use InvalidArgumentException;

class DynamicScraperTest extends TestCase
{
    private DynamicScraper $scraper;

    protected function setUp(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->scraper = new DynamicScraper('/src/node/puppeteerScript.js', 5, $logger);
    }

    public function testScrapeReturnsJobListings(): void
    {
        $mockOutput = json_encode([['title' => 'Software Engineer', 'description' => 'software engineering', 'skills' => 'PostgreSQL, SQL, PHP, AWS, Azure']]);

        $mockProcess = $this->createMock(Process::class);
        $mockProcess->method('run')->willReturn(0); // Return 0 (success)
        $mockProcess->method('isSuccessful')->willReturn(true); // Mock isSuccessful() to return true
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
        $mockProcess->method('run')->willReturnCallback(function () {
            $process = new Process(['node', 'script.js']);
            $process->run(); // Simulate a failed process
            throw new ProcessFailedException($process);
        });

        $this->scraper->setProcess($mockProcess);
        $this->scraper->scrape('https://hiring.cafe/');
    }

    public function testScrapeThrowsExceptionOnMalformedJson(): void
    {
        $this->expectException(\RuntimeException::class);

        $mockProcess = $this->createMock(Process::class);
        $mockProcess->method('run')->willReturn(0); // Return 0 (success)
        $mockProcess->method('isSuccessful')->willReturn(true); // Mock isSuccessful() to return true
        $mockProcess->method('getOutput')->willReturn('{invalid_json');

        $this->scraper->setProcess($mockProcess);
        $this->scraper->scrape('https://hiring.cafe/');
    }

    public function testScrapeReturnsEmptyArrayForEmptyJson(): void
    {
        $mockOutput = json_encode([]); // Empty JSON array

        $mockProcess = $this->createMock(Process::class);
        $mockProcess->method('run')->willReturn(0); // Return 0 (success)
        $mockProcess->method('isSuccessful')->willReturn(true); // Mock isSuccessful() to return true
        $mockProcess->method('getOutput')->willReturn($mockOutput);

        $this->scraper->setProcess($mockProcess);
        $results = $this->scraper->scrape('https://hiring.cafe/');

        $this->assertCount(0, $results);
    }

    public function testScrapeThrowsExceptionForInvalidUrl(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->scraper->scrape('invalid-url');
    }

    public function testScrapeThrowsExceptionOnTimeout(): void
    {
        $this->expectException(ProcessFailedException::class);

        $mockProcess = $this->createMock(Process::class);
        $mockProcess->method('run')->willReturnCallback(function () {
            $process = new Process(['node', 'script.js']);
            $process->setTimeout(1); // Simulate a timeout
            $process->run(); // This will throw a ProcessFailedException due to timeout
            throw new ProcessFailedException($process);
        });

        $this->scraper->setProcess($mockProcess);
        $this->scraper->scrape('https://hiring.cafe/');
    }

    public function testScrapeLogsErrorOnFailure(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
               ->method('error')
               ->with($this->stringContains('Puppeteer scraping failed'));

        $scraper = new DynamicScraper('/src/node/puppeteerScript.js', 5, $logger);

        $mockProcess = $this->createMock(Process::class);
        $mockProcess->method('run')->willReturnCallback(function () {
            $process = new Process(['node', 'script.js']);
            $process->run(); // Simulate a failed process
            throw new ProcessFailedException($process);
        });

        $scraper->setProcess($mockProcess);

        $this->expectException(ProcessFailedException::class);
        $scraper->scrape('https://hiring.cafe/');
    }

    public function testScrapeReturnsMultipleJobListings(): void
    {
        $mockOutput = json_encode([
            ['title' => 'Software Engineer', 'description' => 'software engineering', 'skills' => 'PostgreSQL, SQL, PHP, AWS, Azure'],
            ['title' => 'Data Scientist', 'description' => 'data analysis', 'skills' => 'Python, R, Machine Learning']
        ]);

        $mockProcess = $this->createMock(Process::class);
        $mockProcess->method('run')->willReturn(0); // Return 0 (success)
        $mockProcess->method('isSuccessful')->willReturn(true); // Mock isSuccessful() to return true
        $mockProcess->method('getOutput')->willReturn($mockOutput);

        $this->scraper->setProcess($mockProcess);
        $results = $this->scraper->scrape('https://hiring.cafe/');

        $this->assertCount(2, $results);
        $this->assertEquals('Software Engineer', $results[0]['title']);
        $this->assertEquals('Data Scientist', $results[1]['title']);
    }
}