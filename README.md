# Job Application Automation System

## Overview
A PHP-based solution for automating job application processes with secure web scraping capabilities. Currently implements static and dynamic website scraping with qualification matching in development.

## Features
✅ **Implemented**
- **Web Scraping Engine**
  - Static website scraping using Symfony HttpBrowser
  - Dynamic JS-heavy site scraping via Puppeteer bridge
  - Automatic scraper type detection
- **CLI Interface**
  - Scrape command with URL validation
  - Output formatting (JSON/XML/CSV)
- **Core Architecture**
  - Factory pattern for scraper creation
  - DOM traversal contracts via interfaces
  - Process isolation for Node/Puppeteer operations

🛠 **In Development**
- Qualification matching engine
- ATS optimization module
- Application submission automation

## Security Implementation
### Current Measures
1. **Input Validation**
   ```php
   // ScraperFactory.php
   public function create(string $url): ScraperInterface {
       if (!filter_var($url, FILTER_VALIDATE_URL)) {
           throw new InvalidArgumentException("Invalid URL format");
       }
       // ... factory logic ...
   }
   ```
2. **Process Isolation**
   - Dynamic scrapers run in separate Node processes
   - Memory limits enforced for Puppeteer instances
3. **Error Handling**
   - Custom exceptions for scraping failures
   - Zero sensitive data in error messages

### Planned Security Features
- Rate limiting for scraping operations
- HTML content sanitization
- Environment variable encryption

## Getting Started

### Prerequisites
- PHP 8.3+
- Node.js 18+ (for dynamic scraping)
- Composer 2.5+

### Installation
```bash
git clone https://github.com/your-org/job-application-automation.git
cd job-application-automation
composer install
npm install --prefix ./src/node
```

### CLI Usage
```bash
# Scrape job listings (type auto-detection)
./bin/console scrape https://careers.example.com

# Explicit scraper type with options
./bin/console scrape https://react-app.example/jobs \
  --type=dynamic \
  --timeout=30 \
  --headless
```

## Project Structure
```plaintext
project-root/
├── src/
│   ├── php/
│   │   ├── Contracts/
│   │   │   └── ScraperInterface.php
│   │   ├── StaticScraper.php
│   │   ├── DynamicScraper.php
│   │   └── ScraperFactory.php
│   ├── node/
│   │   └── puppeteer-scraper.js
│   └── cli/
│       └── console
├── tests/
│   ├── StaticScraperTest.php
│   └── DynamicScraperTest.php
└── config/
    └── scraping.yaml
```

## Testing
```bash
# Run all tests with coverage
./vendor/bin/phpunit --coverage-html coverage-report

# Current Coverage (2024-03-15)
# - ScraperFactory: 100%
# - StaticScraper: 92%
# - DynamicScraper: 89%
```

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request.

## Contact

For questions or support, please contact [chegefranklynn@gmail.com](mailto:your-email@example.com).

