const puppeteer = require('puppeteer');

(async () => {
    try {
        const args = process.argv.slice(2);
        let url = args[0]; // URL passed from Laravel
        const path = args[1]; // Screenshot file path passed from Laravel
        if (!url.startsWith('http://') && !url.startsWith('https://')) {
            url = 'https://' + url; // Prepend 'https://' if protocol is missing
        }
        const browser = await puppeteer.launch({
            headless: true, // Run in headless mode (no UI)
            args: ["--no-sandbox", "--disabled-setupid-sandbox"],
        });
        const page = await browser.newPage();
        await page.setViewport({
            width: 1920,  // Desktop width
            height: 768,  // Desktop height
        });

        try {
            await page.goto(url, { timeout: 1800000, waitUntil: 'networkidle2' }); // Ensures page load is complete
            
            // Scroll down the page to ensure all animations are triggered
            await autoScroll(page);

            // Custom delay to ensure animations and transitions are finished
            await delay(5000); // Wait for 3 seconds

            await page.screenshot({
                path: path,
                fullPage: true,
            });

            await browser.close();
            console.log('Screenshot captured successfully.');
        } catch (navigationError) {
            console.error(`Failed to capture screenshot for ${url}:`, navigationError.message);
            process.exit(1);
        }
    } catch (error) {
        console.error('Error capturing screenshot:', error);
        process.exit(1); // Exit with a non-zero status on error
    }
})();

// Helper function to auto-scroll the page
async function autoScroll(page) {
    await page.evaluate(async () => {
        await new Promise((resolve) => {
            let totalHeight = 0;
            const distance = 100; // Scroll distance in pixels
            const timer = setInterval(() => {
                window.scrollBy(0, distance);
                totalHeight += distance;

                if (totalHeight >= document.body.scrollHeight) {
                    clearInterval(timer);
                    resolve();
                }
            }, 100); // Scroll interval in milliseconds
        });
    });
}

// Custom delay function
function delay(time) {
    return new Promise(function(resolve) { 
        setTimeout(resolve, time);
    });
}
