# CHOMIN

CHOMIN is a Laravel storefront and admin system for a Thai fashion shop. It covers catalog browsing, color-library filtering, cart and checkout, PromptPay slip review, coupons, gift cards, points, wishlist, content pages, newsletter capture, and Filament reports.

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=ProductSeeder
php artisan db:seed --class=AdminUserSeeder
php artisan storage:link
npm run build
php artisan serve
```

Use `MAIL_MAILER=log` locally unless you are testing a real SMTP account. Use `QUEUE_CONNECTION=database` with a running worker for queued mail:

```bash
php artisan queue:work
```

## Test And Build

```bash
php artisan test
npm run build
php artisan route:cache
php artisan config:cache
php artisan view:cache
php artisan optimize:clear
```

## Required Environment

Set `APP_NAME=CHOMIN`, `APP_URL`, `APP_LOCALE=th`, database credentials, `FILESYSTEM_DISK=public`, mail credentials, queue settings, and PromptPay/site settings before launch. To bootstrap an admin safely, set `ADMIN_EMAIL`, `ADMIN_PASSWORD`, and optionally `ADMIN_PHONE` before running `AdminUserSeeder`.

## Deployment

GitHub Actions builds assets on the runner, uploads `public/build`, then runs production install, migration, storage link, cache warming, and a `/th` health check on the server. Do not commit SMTP, SSH, database, admin, PromptPay, or deploy webhook secrets.

The optional `deploy.php` webhook requires `DEPLOY_TOKEN` from the server environment and has no default token.

## Production launch checklist

- Confirm legal copy for privacy, terms, shipping, returns, and exchange policy.
- Configure PromptPay ID/name/QR and verify checkout payment instructions.
- Configure SMTP and run a queue worker or supervisor for queued mail.
- Run migrations and seed only intentional production data.
- Set `ADMIN_EMAIL` and `ADMIN_PASSWORD`, then rotate the first admin password after login.
- Verify `storage:link`, product images, color swatches, and `public/build` assets on production.
- Check `/th`, `/th/shop`, product detail, cart, checkout success, `/login`, `/admin`, and reports export.
- Confirm production logs have no 500s or missing critical assets after deploy.
