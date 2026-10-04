# SelfBuy

An e-commerce store built with Laravel 12: a customer storefront (catalogue, cart, wishlist, checkout, order tracking) and an admin panel (catalogue, orders, payments, reports, site settings), deployed through a GitHub Actions pipeline with separate staging and production environments.

**Live:** [selfbuy.live](https://selfbuy.live) · payment gateways run in test mode

## Features

**Storefront**

- Product catalogue with categories, sub-categories, brands and colour/size variants (each variant has its own stock and extra price)
- Cart and wishlist for guests and logged-in customers; a guest's cart/wishlist is merged into their account on login
- Coupons (percentage, fixed, free shipping; date range, usage limit, minimum order, product-specific), welcome and newsletter coupons
- Checkout with Cash on Delivery, Razorpay, Stripe, PayPal and Instamojo
- Customer accounts with email OTP login, Google sign-in and password reset; one account per email and per phone number
- Order tracking with a status timeline, invoice PDF download, product reviews with helpful votes
- Newsletter sign-up, contact form, CMS pages (about, FAQ, policies)
- Branded error pages (404, 419, 429, 500) and a maintenance page; a database outage shows a friendly "temporarily unavailable" page that retries by itself

**Admin panel**

- Dashboard with KPIs, sales chart, top products, payment mix and low-stock alerts
- Order management: status workflow (pending → processing → shipped → delivered / cancelled), tracking details, customer emails, activity history
- Unified transaction history across all gateways and COD
- CSV and PDF exports (dashboard report, orders, transactions)
- Catalogue, coupons, taxes, team, gallery and CMS pages
- Home page content without code: carousel, side banners, partner logos, sign-up offer, newsletter popup, About page images
- **Maintenance mode switch** (System Settings): takes the storefront offline with one click; admin, payment webhooks and payment pages keep working; private preview link
- Loading state on every form button, so nothing is submitted twice

## Tech stack

|                  |                                                     |
| ---------------- | --------------------------------------------------- |
| Backend          | PHP 8.4 (8.2+), Laravel 12, MySQL / MariaDB         |
| Frontend         | Blade, jQuery, Vite                                 |
| PDF / CSV        | barryvdh/laravel-dompdf, streamed CSV               |
| Admin tables     | yajra/laravel-datatables                            |
| Payments         | Razorpay, Stripe, PayPal (Orders API v2), Instamojo |
| Auth             | Email OTP, Google OAuth (Laravel Socialite)         |
| Error monitoring | Bugsnag                                             |
| CI/CD            | GitHub Actions → SSH deploy                         |
| Hosting          | Hostinger (shared hosting with SSH)                 |
| Local debugging  | Laravel Telescope, Debugbar (dev only)              |

## Local setup

Requirements: PHP 8.2+, Composer, MySQL, Node.js. [Laravel Herd](https://herd.laravel.com) works out of the box.

```bash
git clone git@github.com:viveksingh717/selfbuy.git
cd selfbuy

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
# set DB_DATABASE / DB_USERNAME / DB_PASSWORD in .env
# set ADMIN_SEED_PASSWORD (12+ characters) - the password for the seeded admin accounts

php artisan migrate --seed     # schema + admin users, settings and demo catalogue
php artisan storage:link
```

Admin panel: `/admin`. The seeded admin emails are in `database/seeders/AdminUserSeeder.php`; their password comes from `ADMIN_SEED_PASSWORD` in `.env` and is never stored in the repo.

With `MAIL_MAILER=log` (the local default), login OTP codes and all emails are written to `storage/logs/laravel.log` instead of being sent.

## Environments

|                             | local            | staging                        | production                  |
| --------------------------- | ---------------- | ------------------------------ | --------------------------- |
| URL                         | `selfbuy.test`   | `staging.selfbuy.live`         | `selfbuy.live`              |
| Git branch                  | any              | `develop`                      | `master`                    |
| Deployed                    | -                | automatically on push          | manually (Run workflow)     |
| Database                    | local            | its own copy                   | live                        |
| Emails                      | log              | sent ("SelfBuy Staging")       | sent                        |
| Force `https://` URLs       | off              | on (`APP_FORCE_HTTPS=true`)    | on (automatic)              |
| `migrate:fresh` / `db:wipe` | allowed          | allowed                        | blocked                     |
| Telescope                   | loaded           | not loaded                     | not loaded                  |
| Errors sent to Bugsnag      | no               | yes                            | yes                         |

There is one env template, `.env.example`. Its values are for local development; every line that must be different on a server has a `# PRODUCTION:` comment above it. Environment-specific behaviour lives in `AppServiceProvider::configureEnvironment()`.

`APP_DEBUG` is **not** tied to `APP_ENV`. Set `APP_DEBUG=false` on every public server.

## CI/CD

Workflow: [`.github/workflows/deploy.yml`](.github/workflows/deploy.yml)

| Event                                    | What runs                                        |
| ---------------------------------------- | ------------------------------------------------ |
| Pull request to `develop` / `master`     | build + tests                                    |
| Push to `develop`                        | build + tests → **deploy to staging**            |
| Push / merge to `master`                 | build + tests only                               |
| Actions → CI/CD → **Run workflow** (`master`) | build + tests → **deploy to production**    |

- **Build & test:** PHP 8.4 + a throw-away MySQL 8, `npm run build`, migrations on an empty database, `php artisan test`.
- **Deploy (over SSH):** maintenance page on → `git reset --hard` to the tested commit → `composer install --no-dev` → upload the built CSS/JS with rsync → `migrate --force` → `optimize` → maintenance page off. If an admin had already switched maintenance on, the site stays offline after the deploy.
- **If a deploy fails**, the site stays on the maintenance page and a diagnostics step prints the server state and the last Laravel log lines. Every deploy is recorded on the server in `storage/logs/deploy.log`.

Release flow:

```
develop  ──push──▶  staging (auto)  ──test──▶  PR develop → master  ──merge──▶  Run workflow  ──▶  production
```

Required GitHub settings: repository secrets `SSH_HOST`, `SSH_PORT`, `SSH_USER`, `SSH_PRIVATE_KEY`, `SSH_KNOWN_HOSTS`, and environments `staging` / `production` (limited to `develop` / `master`) each with a `DEPLOY_PATH` variable.

## Maintenance mode and error pages

- **Admin → System Settings → Maintenance Mode**, or `php artisan down --render="errors::503" --retry=60 --secret="..."` / `php artisan up`. Both ways are interchangeable.
- Paths that stay open during maintenance are defined in `App\Http\Middleware\PreventRequestsDuringMaintenance` (`admin/*`, `webhooks/*`, `payment/*`).
- Error pages in `resources/views/errors/` are self-contained (no database, no Vite), so they still work when those are down.
- A lost or refused database connection returns the 503 "temporarily unavailable" page (`App\Support\DatabaseUnavailable`); the connection attempt gives up after `DB_CONNECT_TIMEOUT` seconds (default 5).

## Payments

All gateways currently run in **test / sandbox mode**. Keys go in `.env` (see the `RAZORPAY_*`, `STRIPE_*`, `PAYPAL_*`, `INSTAMOJO_*` sections in `.env.example`).

- API base URLs live in `config/services.php`, not in code. `PAYPAL_SANDBOX` and `INSTAMOJO_SANDBOX` choose sandbox or live. Razorpay and Stripe pick the mode from the key type.
- Going live means swapping in live keys and setting the two `*_SANDBOX` flags to `false`. No code changes.
- Webhooks: `POST /webhooks/{razorpay|stripe|paypal|instamojo}`, one per site (staging and production each have their own). They are verified by signature (CSRF-exempt). Instamojo's webhook URL is sent with each payment request, so it needs no dashboard setup.

## Error monitoring (Bugsnag)

Errors reach Bugsnag through the `bugsnag` log channel. On a server:

```env
LOG_STACK=daily,bugsnag
BUGSNAG_API_KEY=your-project-api-key
```

Only `production` and `staging` report by default (`BUGSNAG_NOTIFY_RELEASE_STAGES`), and only `error` level and above (`BUGSNAG_LOGGER_LEVEL`). Lower log levels are attached to each error as breadcrumbs.

## First-time server setup

Deploys are done by CI/CD. These steps are only for setting up a new server:

1. Clone with a read-only GitHub deploy key, point the web root at `public/`, create `.env` from `.env.example` and change every `# PRODUCTION:` line. Most importantly:
    - `APP_ENV=production`, `APP_DEBUG=false`, the real `APP_URL` with https
    - real SMTP for `MAIL_*`. **Login OTPs are sent by email**, so with `MAIL_MAILER=log` nobody can sign in
    - `LOG_STACK=daily,bugsnag`, `LOG_LEVEL=error`, `BUGSNAG_API_KEY`
2. Install and prepare:
    ```bash
    composer install --no-dev --optimize-autoloader
    php artisan key:generate
    php artisan migrate --force
    php artisan storage:link
    php artisan optimize
    ```
3. On a fresh database, seed only what the site needs (not the demo catalogue), with `ADMIN_SEED_PASSWORD` set temporarily in `.env`:
    ```bash
    php artisan db:seed --class=AdminUserSeeder --force
    php artisan db:seed --class=PageSettingSeeder --force
    php artisan db:seed --class=SystemSettingSeeder --force
    ```
4. Make `storage/` and `bootstrap/cache/` writable by the web server.
5. Register the payment webhooks and add `https://your-domain.com/auth/google/callback` in Google Cloud Console.

After any `.env` change on a server, run `php artisan optimize` again. Cached config ignores `.env`.

**Shared hosting (Hostinger) notes:** `proc_open` and PHP's `symlink()` are disabled. Use `composer install --no-scripts` followed by `php artisan package:discover`, and create the storage link with `ln -s ../storage/app/public public/storage` instead of `storage:link`. `php artisan about` does not work there either.

No cron job or queue worker is needed at the moment (`QUEUE_CONNECTION=sync`, no scheduled tasks).

## Project structure

```
app/Http/Controllers         storefront controllers (Admin/, Auth/, Payments/ subfolders)
app/Http/Middleware          incl. PreventRequestsDuringMaintenance (paths open during maintenance)
app/Services                 business logic - controllers stay thin
  CartService                cart, coupons, shipping, totals, guest-cart merge
  WishlistService            wishlist toggle and guest merge
  OrderService               order placement (transaction + row locks)
  AdminOrderService          status workflow, stock restore, tracking, emails
  DashboardService           admin report queries
  TransactionHistoryService  payments + COD in one list
  ExportService              CSV / PDF downloads
  HomeSettingService         home page + About page content (schema-driven admin form)
  SystemSettingService       site-wide settings (logos, contact, SEO...)
  Payments/                  one gateway class per provider + PaymentService
app/Support                  DatabaseUnavailable (friendly 503 on DB outages)
resources/views/errors       standalone error + maintenance pages
config/services.php          third-party keys and API URLs
.github/workflows            CI/CD pipeline
.env.example                 the env template for every environment
```

## Useful commands

```bash
php artisan test                  # run the test suite
php artisan optimize:clear        # clear all caches (local)
php artisan migrate:status        # which migrations have run
composer audit                    # check dependencies for security advisories
```

By Vivek Singh
