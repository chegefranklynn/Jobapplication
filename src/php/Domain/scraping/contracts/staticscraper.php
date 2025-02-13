<?php

declare(strict_types=1);

namespace App\Domain\Scraping\Contracts;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Psr\Log\LoggerInterface; 
use App\Domain\Scraping\Contracts\ScraperInterface;

class StaticScraper implements ScraperInterface
{
    private ?LoggerInterface $logger;
    private HttpBrowser $client;

    /**
     * @param LoggerInterface|null $logger Logger for error logging (optional).
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        $this->client = new HttpBrowser(HttpClient::create());
    }

    public function setHttpBrowser(HttpBrowser $client): void
    {
        $this->client = $client;
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
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid URL: ' . $url);
        }

        try {
            $crawler = $this->client->request('GET', $url);
            $jobs = [];

            $crawler->filter('.job-listing')->each(function ($node) use (&$jobs) {
                $jobs[] = [
                    'title' => $this->safeText($node, '.job-title'),
                    'company' => $this->safeText($node, '.job-company'),
                    'location' => $this->safeText($node, '.job-location'),
                    'description' => $this->safeText($node, '.job-description'),
                    'skills' => $this->safeText($node, '.job-skills'),
                    'environment' => $this->safeText($node, '.job-environment'),
                    'commitment' => $this->safeText($node, '.job-commitment')
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

    private function safeText($node, string $selector): string
    {
        return $node->filter($selector)->count() > 0 
            ? trim($node->filter($selector)->text(''))
            : 'N/A';
    }
}
