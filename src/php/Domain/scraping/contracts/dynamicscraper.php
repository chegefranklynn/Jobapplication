<?php

declare(strict_types=1);
namespace App\Domain\Scraping\Contracts;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Psr\Log\LoggerInterface;

class DynamicScraper implements ScraperInterface
{
    private string $scriptPath;
    private int $timeout;
    private ?LoggerInterface $logger;
    private ?Process $process = null; // Add this property

    public function __construct(string $scriptPath, int $timeout = 30, ?LoggerInterface $logger = null)
    {
        $this->scriptPath = $scriptPath;
        $this->timeout = $timeout;
        $this->logger = $logger;
    }

    // Add this method
    public function setProcess(Process $process): void
    {
        $this->process = $process;
    }

    public function scrape(string $url): array
    {
        // Validate the URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid URL provided: ' . $url);
        }

        // Use $this->process if set, otherwise create a new Process
        $process = $this->process ?? new Process(['node', $this->scriptPath, $url]);
        $process->setTimeout($this->timeout);

        try {
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $output = $process->getOutput();
            $decodedOutput = json_decode($output, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Invalid JSON output from Puppeteer: ' . json_last_error_msg());
            }

            return $decodedOutput;
        } catch (\Exception $e) {
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