<?php

require_once __DIR__ . '/../../vendor/autoload.php';
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use JobApplication\php\ScraperFactory; // Import ScraperFactory


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
            ->addArgument('url', InputArgument::REQUIRED, 'The URL of the website to scrape.')
            ->addOption('type', 't', InputOption::VALUE_OPTIONAL, 'The type of scraper to use (static or dynamic).', 'static');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
{
    $url = $input->getArgument('url');
    $type = $input->getOption('type');

    try {
        $factory = new ScraperFactory();
        $scraper = $factory->create($type, $url);  // Validation happens here
        
        $results = $scraper->scrape($url);

        foreach ($results as $job) {
            $output->writeln("Job Found: " . json_encode($job));
        }

        $output->writeln("Scraping completed successfully.");
        return Command::SUCCESS;
    } catch (\Exception $e) {
        $output->writeln("<error>Error during scraping: " . $e->getMessage() . "</error>");
        return Command::FAILURE;
    }
}
}

// Add Commands to Application
$application->add(new ScrapeCommand());

// Run the CLI Application
$application->run();