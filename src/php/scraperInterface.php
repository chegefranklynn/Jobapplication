<?php

namespace JobApplicationAutomation\Scrapers;

interface ScraperInterface
{
    /**
     * Scrape the given URL and return an array of job listings.
     *
     * @param string $url The URL to scrape.
     * @return array An array of job listings.
     */
    public function scrape(string $url): array;
}
 