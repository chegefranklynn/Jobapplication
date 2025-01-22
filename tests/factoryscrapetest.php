use PHPUnit\Framework\TestCase;
use JobApplicationAutomation\Factory\ScraperFactory;
use JobApplicationAutomation\Scrapers\StaticScraper;
use JobApplicationAutomation\Scrapers\DynamicScraper;
use JobApplicationAutomation\Scrapers\ScraperInterface;
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
        // Uncomment this when DynamicScraper is implemented
        // $scraper = ScraperFactory::createScraper('dynamic');
        // $this->assertInstanceOf(DynamicScraper::class, $scraper);
    }

    public function testCreateUnknownScraperThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ScraperFactory::createScraper('unknown');
    }
}

