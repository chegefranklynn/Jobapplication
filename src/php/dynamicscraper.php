<?php

namespace JobApplicationAutomation\Scrapers;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Psr\Log\LoggerInterface;

class DynamicScraper implements ScraperInterface
{
    private string $scriptPath;
    private int $timeout;
    private ?LoggerInterface $logger;

    /**
     * @param string $scriptPath Path to the Puppeteer script.
     * @param int $timeout Timeout in seconds for the Puppeteer process.
     * @param LoggerInterface|null $logger Logger for error logging (optional).
     */
    public function __construct(string $scriptPath, int $timeout = 30, ?LoggerInterface $logger = null)
    {
        $this->scriptPath = $scriptPath;
        $this->timeout = $timeout;
        $this->logger = $logger;
    }

    /**
     * Scrape the given URL using Puppeteer and return an array of job listings.
     *
     * @param string $url The URL to scrape.
     * @return array An array of job listings.
     * @throws ProcessFailedException If the Puppeteer process fails.
     * @throws \InvalidArgumentException If the URL is invalid.
     */
    public function scrape(string $url): array
    {
        // Validate the URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid URL provided: ' . $url);
        }

        // Create the Puppeteer process
        $process = new Process(['node', $this->scriptPath, $url]);
        $process->setTimeout($this->timeout);

        try {
            // Run the Puppeteer process
            $process->run();

            // Check if the process was successful
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Decode the JSON output into an array
            $output = $process->getOutput();
            $decodedOutput = json_decode($output, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Invalid JSON output from Puppeteer: ' . json_last_error_msg());
            }

            return $decodedOutput;
        } catch (\Exception $e) {
            // Log the error if a logger is provided
            if ($this->logger) {
                $this->logger->error('Puppeteer scraping failed: ' . $e->getMessage(), [
                    'url' => $url,
                    'scriptPath' => $this->scriptPath,
                ]);
            }

            throw $e;
        }
    }
}
