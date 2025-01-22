<?php

namespace JobApplicationAutomation\Scrapers;

interface ScraperInterface
{
    /**
     * Scrape the given URL and return an array of job listings.
     *
     * @param string $url The URL to scrape.
     * @return array<int, array{title: string, company: string, location: string, description: string}> An array of job listings where each listing contains a title, company, location, and description.
     */
    public function scrape(string $url): array;
}
