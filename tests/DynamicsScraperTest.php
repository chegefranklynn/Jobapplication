// tests/testDynamicScraper.php

use PHPUnit\Framework\TestCase;
use JobApplicationAutomation\Scrapers\DynamicScraper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DynamicScraperTest extends TestCase
{
    private DynamicScraper $scraper;

    protected function setUp(): void
    {
        // Mocking the LoggerInterface
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('info')->willReturn(null);
        $logger->method('error')->willReturn(null);

        // Initialize DynamicScraper with mocked logger and a timeout
        $this->scraper = new DynamicScraper($logger, 5);
    }

    public function testScrapeReturnsJobListings(): void
    {
        // Mocking the Puppeteer process for testing
        $url = 'https://hiring.cafe/';
        $mockOutput = json_encode([
            ['title' => 'Data Scientist', 'description' => 'Analyze data.', 'skills' => 'Python, R']
        ]);

        // Simulate the scraping process
        $results = $this->scraper->scrape($url);

        $this->assertCount(1, $results);
        $this->assertEquals('Data Scientist', $results[0]['title']);
        $this->assertEquals('Analyze data.', $results[0]['description']);
        $this->assertEquals('Python, R', $results[0]['skills']);
    }

    public function testScrapeThrowsExceptionOnProcessFailure(): void
    {
        $this->expectException(ProcessFailedException::class);
        // Simulate a failure scenario
        $this->scraper->scrape('https://hiring.cafe/');
    }
}