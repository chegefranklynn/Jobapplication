<?php

return [
    'static' => [
        'class' => 'JobApplicationAutomation\Scrapers\StaticScraper',
        'dependencies' => [
            'logger' => 'YourLoggerService', // Example logger service
            'timeout' => 30, // Default timeout
        ],
    ],
    'dynamic' => [
        'class' => 'JobApplicationAutomation\Scrapers\DynamicScraper',
        'dependencies' => [
            'logger' => 'YourLoggerService', // Example logger service
            'timeout' => 60, // Default timeout
        ],
    ],
];