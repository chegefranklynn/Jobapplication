<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

// CLI Skeleton
$application = new Application('Job Application Automation CLI', '1.0.0');

// Command: scrape
class ScrapeCommand extends Command
{
    protected static $defaultName = 'scrape';

    protected function configure(): void
    {
        $this
            ->setDescription('Scrape job listings from target websites.')
            ->addArgument('url', InputArgument::REQUIRED, 'The URL of the website to scrape.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $url = $input->getArgument('url');

        $output->writeln("Starting scraping for URL: $url");

        try {
            $scraper = new Scraper();
            $results = $scraper->scrape($url);

            foreach ($results as $job) {
                $output->writeln("Job Found: " . json_encode($job));
            }

            $output->writeln("Scraping completed successfully.");
            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln("Error during scraping: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

// Add Commands to Application
$application->add(new ScrapeCommand());

// Run the CLI Application
$application->run();

// Scraper Implementation
class Scraper
{
    public function scrape(string $url): array
    {
        $client = new HttpBrowser(HttpClient::create());
        $crawler = $client->request('GET', $url);

        $jobs = [];
        $crawler->filter('.job-listing')->each(function ($node) use (&$jobs) {
            $jobs[] = [
                'title' => $node->filter('.job-title')->text(''),
                'description' => $node->filter('.job-description')->text(''),
                'skills' => $node->filter('.job-skills')->text('')
            ];
        });

        return $jobs;
    }
}