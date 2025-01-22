<?php

namespace JobApplicationAutomation\Factory;

use JobApplicationAutomation\Scraper\StaticScraper;
use JobApplicationAutomation\Scraper\DynamicScraper;
use JobApplicationAutomation\Scraper\ScraperInterface;
use InvalidArgumentException;

class ScraperFactory
{
    /**
     * Map of scraper types to their corresponding classes.
     *
     * @var array<string, class-string<ScraperInterface>>
     */
    private static array $scraperMap = [
        'static' => StaticScraper::class,
        'dynamic' => DynamicScraper::class,
    ];

    /**
     * Create a scraper instance based on the given type.
     *
     * @param string $type The type of scraper to create ('static' or 'dynamic').
     * @param array $dependencies Constructor dependencies for the scraper.
     * @return ScraperInterface The scraper instance.
     * @throws InvalidArgumentException If the scraper type is unknown or invalid.
     */
    public static function createScraper(string $type, array $dependencies = []): ScraperInterface
    {
        if (!array_key_exists($type, self::$scraperMap)) {
            throw new InvalidArgumentException("Unknown scraper type: $type");
        }

        $scraperClass = self::$scraperMap[$type];

        if (!is_subclass_of($scraperClass, ScraperInterface::class)) {
            throw new InvalidArgumentException("Invalid scraper class for type: $type");
        }

        return new $scraperClass(...$dependencies);
    }
}
