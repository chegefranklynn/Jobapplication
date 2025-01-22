<?php

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class StaticScraper implements ScraperInterface{

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
