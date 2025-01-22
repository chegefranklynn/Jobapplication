<?php

namespace JobApplicationAutomation\Scraper;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Psr\Log\LoggerInterface;

class StaticScraper implements ScraperInterface
{
    private ?LoggerInterface $logger;

    /**
     * @param LoggerInterface|null $logger Logger for error logging (optional).
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Scrape the given URL and return an array of job listings.
     *
     * @param string $url The URL to scrape.
     * @return array<int, array{title: string, company?: string, location?: string, description: string, skills: string}> 
     * @throws \InvalidArgumentException If the URL is invalid.
     * @throws \RuntimeException If scraping fails due to missing elements.
     */
    public function scrape(string $url): array
    {
        // Validate the URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid URL provided: ' . $url);
        }

        // Initialize the HTTP client
        $client = new HttpBrowser(HttpClient::create());

        try {
            $crawler = $client->request('GET', $url);
            $jobs = [];

            $crawler->filter('.job-listing')->each(function ($node) use (&$jobs) {
                $jobs[] = [
                    'title' => $node->filter('.job-title')->count() > 0 ? $node->filter('.job-title')->text('') : 'N/A',
                    'company' => $node->filter('.job-company')->count() > 0 ? $node->filter('.job-company')->text('') : 'N/A',
                    'location' => $node->filter('.job-location')->count() > 0 ? $node->filter('.job-location')->text('') : 'N/A',
                    'description' => $node->filter('.job-description')->count() > 0 ? $node->filter('.job-description')->text('') : 'N/A',
                    'skills' => $node->filter('.job-skills')->count() > 0 ? $node->filter('.job-skills')->text('') : 'N/A',
                ];
            });

            // Log successful scraping
            if ($this->logger) {
                $this->logger->info('Scraping completed successfully for URL: ' . $url, ['jobCount' => count($jobs)]);
            }

            return $jobs;
        } catch (\Exception $e) {
            // Log errors
            if ($this->logger) {
                $this->logger->error('Scraping failed for URL: ' . $url, ['error' => $e->getMessage()]);
            }
            throw new \RuntimeException('Error during scraping: ' . $e->getMessage());
        }
    }
}
