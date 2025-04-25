Retro Gadgets Catalog
An open-source Laravel-based catalog of retro devices from the 70s, 80s, 90s, and early 2000s — with automatic price updates from external sources and SEO-optimized content.

Features
Built with Laravel 10 and PostgreSQL

Full-text search and filtering by category, year, and name

Service + Repository architecture (clean and testable)

Automatic price updates from eBay, Prom.ua, and OLX

Daily background parser using queues, scheduler, and caching

SEO-ready: dynamic meta tags, Schema.org markup, readable slugs

Price history, fun facts, market competition, and unique features

Admin panel with a built-in form builder (WIP)

Technologies
Backend: PHP 8.2, Laravel 10

Database: PostgreSQL, Redis (for cache and queues)

API: eBay Browse API, Prom.ua, OLX (scraping)

Frontend: Blade, Bootstrap 5, noUiSlider

Testing: PHPUnit (unit and feature tests)

DevOps: GitHub Actions (CI), Laravel Scheduler, Queues

Deployment: Render, Railway, or your own VPS

Setup
git clone https://github.com/IliyaDovgopol/retro-gadgets
cd retro-gadgets

cp .env.example .env
composer install
php artisan key:generate

# Set up database (PostgreSQL preferred)
php artisan migrate --seed

# Start queue and optionally reveal one gadget
php artisan queue:work
php artisan gadgets:reveal-one


Make sure you have:

PostgreSQL

Redis

EBAY_CLIENT_ID and EBAY_CLIENT_SECRET in your .env file

## Scheduled Jobs

To enable automatic price updates, set up the Laravel scheduler.

### On Linux or VPS (using cron)

Add this to your crontab:

```bash
* * * * * php /home/IliyaDovgopol/retro-gadgets/artisan schedule:run >> /dev/null 2>&1

On Windows

Use Windows Task Scheduler to run the following command every minute:

php C:\Projects\retro-gadgets\artisan schedule:run

You can also create a .bat file and schedule it using the Task Scheduler GUI.

License
MIT — free for personal and commercial use.

Author
Developed by Iliya Dovgopol
https://github.com/IliyaDovgopol