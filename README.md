# SelfBuy (Vivek Singh)

An e-commerce store built with Laravel 12: a customer storefront (catalogue, cart, wishlist, checkout, order tracking) and an admin panel (catalogue, orders, payments, reports, site settings).

## Features

**Storefront**

- Product catalogue with categories, sub-categories, brands and colour/size variants (each variant has its own stock and extra price)
- Cart and wishlist for guests and logged-in customers; a guest's cart/wishlist is merged into their account on login
- Coupons (percentage, fixed, free shipping; date range, usage limit, minimum order, product-specific)
- Checkout with Cash on Delivery, Razorpay, Stripe, PayPal and Instamojo
- Customer accounts with email OTP login, Google sign-in and password reset
- Order tracking with a status timeline, invoice PDF download, product reviews with helpful votes
- Newsletter sign-up, contact form, CMS pages (about, FAQ, policies)

**Admin panel**

- Dashboard with KPIs, sales chart, top products, payment mix and low-stock alerts
- Order management: status workflow (pending → processing → shipped → delivered / cancelled), tracking details, customer emails, activity history
- Unified transaction history across all gateways and COD
- CSV and PDF exports (dashboard report, orders, transactions)
- Catalogue, coupons, taxes, banners, home page, team, gallery and system settings

## Tech stack

|                  |                                                     |
| ---------------- | --------------------------------------------------- |
| Backend          | PHP 8.2+, Laravel 12, MySQL                         |
| Frontend         | Blade, jQuery, Vite                                 |
| PDF / CSV        | barryvdh/laravel-dompdf, streamed CSV               |
| Admin tables     | yajra/laravel-datatables                            |
| Payments         | Razorpay, Stripe, PayPal (Orders API v2), Instamojo |
| Error monitoring | Bugsnag                                             |
| Local debugging  | Laravel Telescope, Debugbar (dev only)              |

## Local setup

Requirements: PHP 8.2+, Composer, MySQL, Node.js. [Laravel Herd](https://herd.laravel.com) works out of the box.

```bash
git clone https://github.com/viveksingh717/selfbuy.git
cd selfbuy

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
# set DB_DATABASE / DB_USERNAME / DB_PASSWORD in .env

php artisan migrate --seed     # schema + admin user, settings and demo catalogue
php artisan storage:link
```

Admin panel: `/admin`. Seeded admin accounts are defined in `database/seeders/AdminUserSeeder.php`.

With `MAIL_MAILER=log` (the local default), login OTP codes and all emails are written to `storage/logs/laravel.log` instead of being sent.

## Environments

There is one env template, `.env.example`. Its values are for local development. Every line that must be different on the live server has a `# PRODUCTION:` comment above it.

What changes automatically with `APP_ENV` (see `AppServiceProvider::configureEnvironment()`):

|                             | local   | staging    | production                   |
| --------------------------- | ------- | ---------- | ---------------------------- |
| Force `https://` URLs       | off     | off        | on (`APP_FORCE_HTTPS`)       |
| Secure session cookie       | off     | off        | on (`SESSION_SECURE_COOKIE`) |
| `migrate:fresh` / `db:wipe` | allowed | allowed    | blocked                      |
| Telescope                   | loaded  | not loaded | not loaded                   |
| Errors sent to Bugsnag      | no      | yes        | yes                          |

`APP_DEBUG` is **not** tied to `APP_ENV`. Set `APP_DEBUG=false` on every public server.

## Payments

All gateways currently run in **test / sandbox mode**. Keys go in `.env` (see the `RAZORPAY_*`, `STRIPE_*`, `PAYPAL_*`, `INSTAMOJO_*` sections in `.env.example`).

- API base URLs live in `config/services.php`, not in code. `PAYPAL_SANDBOX` and `INSTAMOJO_SANDBOX` choose sandbox or live. Razorpay and Stripe pick the mode from the key type.
- Going live means swapping in live keys and setting the two `*_SANDBOX` flags to `false`. No code changes.
- Webhooks: `POST /webhooks/{razorpay|stripe|paypal|instamojo}`. Register them in each gateway dashboard with the server's domain. They are verified by signature (CSRF-exempt).

## Error monitoring (Bugsnag)

Errors reach Bugsnag through the `bugsnag` log channel. On a server:

```env
LOG_STACK=daily,bugsnag
BUGSNAG_API_KEY=your-project-api-key
```

Only `production` and `staging` report by default (`BUGSNAG_NOTIFY_RELEASE_STAGES`), and only `error` level and above (`BUGSNAG_LOGGER_LEVEL`). Lower log levels are attached to each error as breadcrumbs.

## Deploying to production

1. Create `.env` from `.env.example` and change every `# PRODUCTION:` line. Most importantly:
    - `APP_ENV=production`, `APP_DEBUG=false`, the real `APP_URL` with https
    - real SMTP for `MAIL_*`. **Login OTPs are sent by email**, so with `MAIL_MAILER=log` nobody can sign in
    - `LOG_STACK=daily,bugsnag`, `LOG_LEVEL=error`, `BUGSNAG_API_KEY`
2. Install and build:
    ```bash
    composer install --no-dev --optimize-autoloader
    npm ci && npm run build
    php artisan key:generate          # first deploy only
    php artisan migrate --force
    php artisan storage:link          # first deploy only
    php artisan optimize              # caches config, routes, views, events
    ```
3. On the first deploy, seed only what the site needs (not the demo catalogue), then change the admin password:
    ```bash
    php artisan db:seed --class=AdminUserSeeder --force
    php artisan db:seed --class=PageSettingSeeder --force
    php artisan db:seed --class=SystemSettingSeeder --force
    ```
4. Make `storage/` and `bootstrap/cache/` writable by the web server.
5. Register the payment webhooks and add `https://your-domain.com/auth/google/callback` in Google Cloud Console.

After any `.env` change on the server, run `php artisan optimize` again. Cached config ignores `.env`.

No cron job or queue worker is needed at the moment: nothing is queued and there are no scheduled tasks.

## Project structure

```
app/Http/Controllers         storefront controllers (Admin/, Auth/, Payments/ subfolders)
app/Services                 business logic - controllers stay thin
  CartService                cart, coupons, shipping, totals, guest-cart merge
  WishlistService            wishlist toggle and guest merge
  OrderService               order placement (transaction + row locks)
  AdminOrderService          status workflow, stock restore, tracking, emails
  DashboardService           admin report queries
  TransactionHistoryService  payments + COD in one list
  ExportService              CSV / PDF downloads
  Payments/                  one gateway class per provider + PaymentService
config/services.php          third-party keys and API URLs
.env.example                 the env template for every environment
```

## Useful commands

```bash
php artisan test                  # run the test suite
php artisan about                 # environment, debug mode, cache status
php artisan optimize:clear        # clear all caches (local)
composer audit                    # check dependencies for security advisories
```

BY Vivek Singh
