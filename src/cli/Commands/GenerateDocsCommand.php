<?php

declare(strict_types=1);

namespace App\cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateDocsCommand extends Command
{
    protected static $defaultName = 'docs:generate';
    protected static $defaultDescription = 'Generates project documentation';

    public function __construct(
        private string $docsPath = __DIR__.'/../../../docs/reports/progress-2024-03.md'
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        if (!file_exists($this->docsPath)) {
            $io->error('Documentation source not found: '.$this->docsPath);
            return Command::FAILURE;
        }

        $content = file_get_contents($this->docsPath);
        $html = $this->convertToHtml($content);
        
        $outputDir = dirname($this->docsPath).'/output';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        
        file_put_contents($outputDir.'/report.html', $html);
        $io->success('Documentation generated at: '.realpath($outputDir));
        
        return Command::SUCCESS;
    }

    private function convertToHtml(string $markdown): string
    {
        // Simple conversion for ASCII diagrams
        $html = nl2br(htmlspecialchars($markdown));
        $html = preg_replace('/^=+$/m', '<hr>', $html);
        $html = preg_replace('/\b([A-Z][a-z]+)\b/', '<strong>$1</strong>', $html);
        
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <title>Project Documentation</title>
            <style>
                body { font-family: monospace; margin: 2em; }
                strong { color: #2c3e50; }
                hr { border: 1px solid #ccc; }
            </style>
        </head>
        <body>$html</body>
        </html>
        HTML;
    }
} 