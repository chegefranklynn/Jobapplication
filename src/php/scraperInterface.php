<?php

namespace JobApplicationAutomation\Scrapers;

interface ScraperInterface
{
    /**
     * Scrape the given URL and return an array of job listings.
     *
     * @param string $url The URL to scrape. Must be a valid URL.
     * @return array<int, array{
     *     title: string,
     *     company?: string|null,
     *     location?: string|null,
     *     description: string,
     *     additionalFields?: array<string, mixed>
     * }> An array of job listings. Each listing includes:
     *     - title (string, required)
     *     - company (string|null, optional)
     *     - location (string|null, optional)
     *     - description (string, required)
     *     - additionalFields (array, optional) for any extra data.
     * @throws \InvalidArgumentException If the URL is invalid.
     * @throws \RuntimeException If scraping fails due to network issues or unexpected HTML structure.
     */
    public function scrape(string $url): array;
}
