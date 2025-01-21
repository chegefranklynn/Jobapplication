<?php

class ScraperFactory
{
    /**
     * Create a scraper instance based on the given type.
     *
     * @param string $type The type of scraper to create ('static' or 'dynamic').
     * @return ScraperInterface The scraper instance.
     * @throws InvalidArgumentException If the scraper type is unknown.
     */
    public static function createScraper(string $type): ScraperInterface
    {
        switch ($type) {
            case 'static':
                return new StaticScraper();
            // case 'dynamic':
            //     return new DynamicScraper(); // Uncomment and implement when DynamicScraper is ready
            default:
                throw new InvalidArgumentException("Unknown scraper type: $type");
        }
    }
}