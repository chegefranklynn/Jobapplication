#!/bin/bash

# CLI execution script for Job Application Automation System

# Define PHP binary
PHP_BIN=$(which php)

# Check if PHP is installed
if [ -z "$PHP_BIN" ]; then
    echo "PHP is not installed or not available in the PATH. Please install PHP and try again."
    exit 1
fi

# Check if vendor/autoload.php exists
if [ ! -f "$(dirname "$0")/../vendor/autoload.php" ]; then
    echo "Composer dependencies are missing. Please run 'composer install' in the project root."
    exit 1
fi

# Run the Symfony Console application
$PHP_BIN "$(dirname "$0")/../src/cli/run.php" "$@"