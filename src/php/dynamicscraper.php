<?php

namespace YourNamespace; // Replace with your actual namespace

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DynamicScraper implements ScraperInterface
{
    /**
     * Scrape the given URL using Puppeteer and return an array of job listings.
     *
     * @param string $url The URL to scrape.
     * @return array An array of job listings.
     * @throws ProcessFailedException If the Puppeteer process fails.
     */
    public function scrape(string $url): array
    {
        // Path to the Node.js script that uses Puppeteer
        $scriptPath = __DIR__ . '/../scripts/puppeteerScraper.js';

        // Create a new process to run the Node.js script
        $process = new Process(['node', $scriptPath, $url]);
        $process->run();

        // Check if the process was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Get the output from the Node.js script
        $output = $process->getOutput();

        // Decode the JSON output into an array
        return json_decode($output, true);
    }
}
