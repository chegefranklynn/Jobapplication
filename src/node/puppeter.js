const puppeteer = require('puppeteer');

(async () => {
    const url = process.argv[2]; // Get the URL from command line arguments
    const browser = await puppeteer.launch({ headless: true }); // Launch browser in headless mode
    const page = await browser.newPage();
    await page.goto(url, { waitUntil: 'networkidle2' }); // Wait until the network is idle

    // Scrape job listings (modify the selector as needed)
    const jobs = await page.evaluate(() => {
        const jobListings = [];
        const elements = document.querySelectorAll('.job-listing'); // Adjust the selector based on the website structure
        elements.forEach(element => {
            jobListings.push({
                title: element.querySelector('.job-title') ? element.querySelector('.job-title').innerText : '',
                description: element.querySelector('.job-description') ? element.querySelector('.job-description').innerText : '',
                skills: element.querySelector('.job-skills') ? element.querySelector('.job-skills').innerText : ''
            });
        });
        return jobListings;
    });

    console.log(JSON.stringify(jobs)); // Output the results as JSON
    await browser.close();
})();