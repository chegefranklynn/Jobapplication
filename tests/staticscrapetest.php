use PHPUnit\Framework\TestCase;
use JobApplicationAutomation\Scrapers\StaticScraper;
use Psr\Log\LoggerInterface;

class StaticScraperTest extends TestCase
{
    private StaticScraper $scraper;

    protected function setUp(): void
    {
        // Mocking the LoggerInterface
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('info')->willReturn(null);
        $logger->method('error')->willReturn(null);

        // Initialize StaticScraper with mocked logger and a timeout
        $this->scraper = new StaticScraper($logger, 5);
    }

    public function testScrapeReturnsJobListings(): void
    {
        // Mocking the HTTP response for testing
        $url = 'https://hiring.cafe/';
        
        $mockHtml = '<div class="job-listing">
                        <div class="job-title">Software Engineer</div>
                        <div class="job-description">Develop software solutions.</div>
                        <div class="job-skills">PHP, JavaScript</div>
                     </div>';
        
        // Simulate the scraping process
        $results = $this->scraper->scrape($url);

        $this->assertCount(1, $results);
        $this->assertEquals('Software Engineer', $results[0]['title']);
        $this->assertEquals('Develop software solutions.', $results[0]['description']);
        $this->assertEquals('PHP, JavaScript', $results[0]['skills']);
    }

    // Additional tests for edge cases can be added here
}

