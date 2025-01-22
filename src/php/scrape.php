<?php

namespace JobApplicationAutomation\Console;

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use JobApplicationAutomation\Factory\ScraperFactory;

$application = new Application('Job Application Automation CLI', '1.0.0');

class ScrapeCommand extends Command
{
    protected static $defaultName = 'scrape';

    protected function configure(): void
    {
        $this
            ->setDescription('Scrape job listings from target websites.')
            ->addArgument('url', InputArgument::REQUIRED, 'The URL of the website to scrape.')
            ->addOption('type', 't', InputOption::VALUE_OPTIONAL, 'The type of scraper to use (static or dynamic).', 'static')
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Path to save the scraped results in JSON format.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $url = $input->getArgument('url');
        $type = $input->getOption('type');
        $outputPath = $input->getOption('output');

        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $output->writeln("<error>Invalid URL provided: $url</error>");
            return Command::FAILURE;
        }

        $output->writeln("Starting scraping for URL: $url using $type scraper...");

        try {
            // Create the scraper using the factory
            $scraper = ScraperFactory::createScraper($type);
            $results = $scraper->scrape($url);

            // Display the results
            foreach ($results as $job) {
                $output->writeln("Job Found: " . json_encode($job));
            }

            // Save results to a file if an output path is provided
            if ($outputPath) {
                file_put_contents($outputPath, json_encode($results, JSON_PRETTY_PRINT));
                $output->writeln("Results saved to: $outputPath");
            }

            $output->writeln("Scraping completed successfully.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            // Log error with timestamp
            $errorLog = [
                'timestamp' => date('Y-m-d H:i:s'),
                'error' => $e->getMessage(),
                'url' => $url,
                'type' => $type,
            ];

            file_put_contents(__DIR__ . '/../logs/application.log', json_encode($errorLog) . PHP_EOL, FILE_APPEND);
            $output->writeln("<error>Error during scraping: " . $e->getMessage() . "</error>");
            return Command::FAILURE;
        }
    }
}

$application->add(new ScrapeCommand());
$application->run();
