# Template Magento

Empty Magento 2.4.9 project prepared as a base for Rokezzz Magento projects.

The project contains a Docker-based local environment, basic Magento infrastructure services, custom Rokezzz modules and code style tooling for PHP files in `app/code/Rokezzz`.

Main project assumptions:

- Magento source code is kept in the `src` directory.
- Custom project modules are kept in `src/app/code/Rokezzz`.
- Local environment is managed by `compose.yaml`.
- PHP code style is checked by PHP-CS-Fixer and PHPCS.
- Pull requests are checked by GitHub Actions.

## Docker Environment

The local environment is defined in `compose.yaml` and contains:

- `php` - PHP-FPM container based on PHP 8.5 with Magento-related extensions and Composer.
- `nginx` - web server serving Magento from `src/pub`.
- `varnish` - HTTP cache exposed on the local `HTTP_PORT`, by default `8080`.
- `db` - MariaDB 11.8 database.
- `opensearch` - OpenSearch service used by Magento search.
- `valkey` - Redis-compatible cache backend.
- `rabbitmq` - message queue with management panel.
- `mailhog` - local SMTP catcher for testing outgoing emails.
- `vite` - Node.js dev server for frontend HMR (Hot Module Replacement), port `5173`.

Docker configuration files are located in:

- `docker/php/Dockerfile`
- `docker/nginx/default.conf`
- `docker/varnish/default.vcl`

Before starting the project, copy the example environment file:

```bash
cp .env.example .env
```

Adjust values in `.env` if needed, especially ports, passwords and `HOST_UID` / `HOST_GID`.

Start the environment:

```bash
```

Enter the PHP container:

```bash
docker compose exec php bash
```

## Mailhog

Outgoing emails from Magento are routed through Mailhog via `msmtp` in the PHP container. No extra Magento SMTP configuration is required for local development.

- Web UI: `http://localhost:8025` (override with `MAILHOG_UI_PORT` in `.env`)
- SMTP port: `1025` (override with `MAILHOG_SMTP_PORT` in `.env`)

After adding Mailhog, rebuild the PHP image so `msmtp` is installed:

```bash
```

## First Install

To install Magento after the environment is running, execute the installation command inside the PHP container.

Example local installation:

```bash
php bin/magento setup:install \
    --base-url=http://localhost:8080/ \
    --db-host=db \
    --db-name=magento \
    --db-user=magento \
    --db-password=magento \
    --admin-firstname=admin \
    --admin-lastname=admin \
    --admin-email=admin@rokezzz.pl \
    --admin-user=admin \
    --admin-password='Admin123!' \
    --language=pl_PL \
    --currency=PLN \
    --timezone=Europe/Warsaw \
    --use-rewrites=1 \
    --search-engine=opensearch \
    --opensearch-host=opensearch \
    --opensearch-port=9200 \
    --cache-backend=redis \
    --cache-backend-redis-server=valkey \
    --cache-backend-redis-password="${VALKEY_PASSWORD}" \
    --cache-backend-redis-db=0 \
    --page-cache=redis \
    --page-cache-redis-server=valkey \
    --page-cache-redis-password="${VALKEY_PASSWORD}" \
    --page-cache-redis-db=1 \
    --amqp-host=rabbitmq \
    --amqp-port=5672 \
    --amqp-user="${RABBITMQ_USER}" \
    --amqp-password="${RABBITMQ_PASSWORD}" \
    --backend-frontname=admin
```

After installation, run:

```bash
php bin/magento setup:upgrade
php bin/magento cache:flush
```

## Varnish

The `varnish` container sits in front of `nginx` and is exposed on `HTTP_PORT` (default `8080`). Magento install above still sets Redis as the page cache, so after installation switch full-page cache to Varnish and point Magento at the Docker service name. Run this inside the PHP container:

```bash
php bin/magento config:set --lock-env system/full_page_cache/caching_application 2
php bin/magento config:set --lock-env system/full_page_cache/varnish/backend_host varnish
php bin/magento cache:flush
```

`--lock-env` writes the values to `app/etc/env.php` (`2` = Varnish). The VCL used by the container is `docker/varnish/default.vcl` (backend `nginx:80`).

## Admin Credentials

Default credentials from the example installation command:

- URL: `http://localhost:8080/admin`
- User: `admin`
- Password: `Admin123!`

## Rokezzz Modules

Custom modules are located in `src/app/code/Rokezzz`.

The **Rokezzz** item in the admin menu (second from the left) groups content tools and **Administracja → Ustawienia**, which opens Rokezzz store configuration. Module settings also appear under **Stores → Configuration**: a **Rokezzz** tab for homepage features, and **General** sections for the remaining modules.

### Platform

#### Rokezzz_Core

Adds the Rokezzz admin menu and homepage feature toggles in **Stores → Configuration → Rokezzz → Ustawienia Główne**: master enable, promoted categories (with optional images), homepage slider and bestsellers. Category attributes for promoted listing and image display are created by data patches.

#### Rokezzz_Settings

Company identity used on invoices, emails and KSeF XML. Configure it in **Stores → Configuration → General → Rokezzz Settings** (or **Rokezzz → Administracja → Ustawienia**): name, NIP, REGON, address, bank account, contact data, logo, invoice SystemInfo and KSeF API URL / token / NIP.

#### Rokezzz_Language

Polish storefront and admin defaults via a data patch: country `PL`, locale `pl_PL`, timezone `Europe/Warsaw`, weight `kgs`, currency `PLN`. Also loads extra translation dictionaries so Magento labels stay in Polish unless the admin locale is changed.

#### Rokezzz_Cron

Sets every Magento indexer to **Update by Schedule** after `setup:upgrade` (replaces the old `Rokezzz_IndexerSchedule` module).

#### Rokezzz_ReCaptcha

Stores initial reCAPTCHA configuration for frontend and backend forms. It configures selected forms to use reCAPTCHA v3 and disables the legacy admin captcha configuration.

#### Rokezzz_Email

Overrides Magento email header and footer templates. Footer uses store contact data and the “System wspierany przez Rokezzz” signature. Templates remain editable in **Marketing → Communications → Email Templates**.

The module ships default config for those templates. After installation (or on an existing store), enable them inside the PHP container:

```bash
php bin/magento config:set design/email/header_template rokezzz_email_header_template
php bin/magento config:set design/email/footer_template rokezzz_email_footer_template
php bin/magento cache:flush
```

#### Rokezzz_ViteDev

Development-only bridge to the Vite server (`vite/`, Docker service `vite`). Injects Vite scripts on the storefront when port `5173` is up; register edited SCSS/LESS in `vite/src/frontend.scss`. Disable in production.

#### Rokezzz_Styleguide

Preview endpoint for theme components at `/styleguide`. Intended for local/design work, not production traffic.

### Catalog

#### Rokezzz_Category

Dynamic **New Arrivals** and **Bestsellers** categories, each with a hide toggle in **Stores → Configuration → General → Rokezzz Category**. New products can show a “new” label from Magento news-from / news-to dates.

#### Rokezzz_CategoryAdvance

Category PDF catalog download (Twig template). Toggle it in **Stores → Configuration → General → Rokezzz Category Advance → Enable category PDF catalog**. When disabled, the download button is hidden on category pages.

#### Rokezzz_CatalogListing

Async, fragment-based category and search listing: filters, sorting, pagination and view mode update without a full page reload.

#### Rokezzz_MostPopular

Widget **Rokezzz Most Popular Products** based on Magento sales reports. Toggle defaults in **Stores → Configuration → General → Rokezzz Most Popular** (count and report period).

#### Rokezzz_Compare

Enables Magento product compare via a data patch (`rokezzz_compare/general/enabled`, `catalog/recently_products/compared_count`). Toggle it in **Stores → Configuration → General → Rokezzz Compare → Product compare → Enable product compare**. When disabled, add-to-compare links and the compare sidebar are hidden on the storefront.

#### Rokezzz_Wishlist

Enables Magento core wishlist (schowek) via a data patch (`wishlist/general/active`). Toggle it in **Stores → Configuration → General → Rokezzz Wishlist → Schowek → Enable wishlist**. Sharing can be turned off separately. When disabled, wishlist UI and controllers are blocked on the storefront.

#### Rokezzz_AvailabilityNotification

Wraps Magento core back-in-stock alerts (`catalog/productalert/allow_stock`). After `setup:upgrade`, availability notifications are **disabled** by default. Toggle them in **Stores → Configuration → General → Rokezzz Availability Notification → Product availability alerts → Enable availability notifications**. When disabled, the subscribe form on out-of-stock product pages is hidden and direct subscription requests are blocked on the storefront.

#### Rokezzz_ProductsReview

Enables Magento core product reviews via a data patch (`catalog/review/active`, `catalog/review/allow_guest`). Toggle them in **Stores → Configuration → General → Rokezzz Product Reviews**. New reviews are **Pending** until approved in **Marketing → User Content → All Reviews**.

#### Rokezzz_Omnibus

Tracks the lowest gross price of simple products over a configurable period (default 30 days) and shows it when a promotion is active (EU Omnibus). Toggle it in **Stores → Configuration → General → Rokezzz Omnibus**. Prices can also be recorded with `rokezzz:omnibus:record-prices`.

#### Rokezzz_Tax

Project tax configuration and a helper console command for product prices.

The data patch creates Polish tax rates for `0%`, `5%`, `8%` and `23%`, creates the main tax rule and saves basic tax-related store configuration, including shipping origin and tax calculation settings (prices entered as gross). Adjust catalog prices with:

```bash
php bin/magento rokezzz:tax:adjust-prices 23 up
```

### Customer

#### Rokezzz_CustomerNip

Shows the Magento `taxvat` (NIP) field on registration and account edit. After registration, customers are assigned to a private-person or company customer group based on whether NIP was provided.

#### Rokezzz_Gus

Looks up company data from the GUS REGON API by NIP. Toggle it in **Stores → Configuration → General → Rokezzz GUS** and set the API key from api.stat.gov.pl.

#### Rokezzz_CustomerContact

Assigns each customer to an admin user (account manager). Logged-in customers see that person’s contact data on the contact form, and submitted messages are routed to the assigned admin when possible.

#### Rokezzz_CustomerAccountDeletion

Lets a logged-in customer delete their own account from the customer account area (`/customerdelete`), after password confirmation.

#### Rokezzz_RememberMe

Optional longer sessions on storefront and admin login (“remember me”). Lifetimes are in **Stores → Configuration → General → Rokezzz Settings → Remember Me**.

#### Rokezzz_Legal

Creates CMS pages **Regulamin** (`terms-and-conditions`), **Polityka prywatności** (`privacy-policy`) and **RODO** (`rodo`), and a checkout agreement checkbox. Edit pages in **Content → Pages** and the agreement in **Stores → Terms and Conditions**.

#### Rokezzz_Orders

Optional order notes field in checkout. Toggle it in **Stores → Configuration → General → Rokezzz Orders → Order notes**.

#### Rokezzz_OrderAdvance

Customer’s own order number in checkout, plus a storefront status bar for the latest order. Toggle both in **Stores → Configuration → General → Rokezzz Order Advance**.

#### Rokezzz_OrderOwnerNotification

Sends an optional email to the store owner after each new order. Toggle it in **Stores → Configuration → General → Rokezzz Order Owner Notification → Store owner order notification** (disabled by default); set the recipient address or leave it empty to use the store general contact email.

#### Rokezzz_ResubmittingArchivedOrders

Archives old orders and lets customers resubmit them. Toggle it in **Stores → Configuration → General → Rokezzz Resubmitting Archived Orders** (age in days and eligible statuses).

#### Rokezzz_DeliveryDate

Preferred delivery date in checkout. Toggle it in **Stores → Configuration → General → Rokezzz Settings → Delivery date** (min/max days ahead, weekends, required flag).

#### Rokezzz_Delivery

Disables Magento multi-address checkout by default. Also registers five placeholder shipping methods (disabled until enabled) in **Stores → Configuration → Sales → Delivery Methods**.

#### Rokezzz_EmptyShipping

The original five placeholder carriers (`Empty shipping method 1–5`) with enabled / name / price / sort order. Same **Delivery Methods** screen as `Rokezzz_Delivery`; enable only the methods you need.

#### Rokezzz_InPost

Configure InPost in **Stores → Configuration → General → Rokezzz InPost → Enable InPost integration** (default: disabled), set ShipX credentials in **Sales → Shipping Settings → InPost**, and enable delivery methods in **Sales → Delivery Methods**.

#### Rokezzz_Offers

Quote requests for products (including out-of-stock items that cannot go to a normal cart). Admins review requests and approve offers (PDF + email with a link) under **Rokezzz → Content → Offer Requests** and **Offers**. Customers see offers in the account panel and can apply an accepted offer to the cart.

#### Rokezzz_LoyaltyPoints

Earn points on completed orders and redeem them at checkout. Balance is shown in the customer account. Toggle rates and limits in **Stores → Configuration → General → Rokezzz Settings → Loyalty Points**.

#### Rokezzz_ContractWithdrawal

EU contract withdrawal requests from the customer order view. Toggle period (days) in **Stores → Configuration → General → Rokezzz Settings → Contract withdrawal**.

#### Rokezzz_Invoice

Polish invoices and credit notes (Twig PDF + FA(3) XML), including the Rokezzz footer signature. Optional KSeF submission after the configured start date. Toggle the module and KSeF in **Stores → Configuration → General → Rokezzz Invoice**; company and API credentials live in **Rokezzz Settings**. Manual send:

```bash
php bin/magento rokezzz:invoices:send-ksef
```

#### Rokezzz_Cms

Creates the editable homepage **O nas** CMS block (`rokezzz_about`). Edit it in **Content → Blocks**.

#### Rokezzz_Slider

Promotion slider managed in **Rokezzz → Content → Slider**. Enable the homepage slider in **Stores → Configuration → Rokezzz → Ustawienia Główne**.

#### Rokezzz_Popup

Promotional popup whose content is a CMS block. Enable it, pick the block and delay in **Stores → Configuration → Rokezzz → Ustawienia Główne → Moduł Pop-up** (requires the Core master switch).

#### Rokezzz_Cookies

Storefront cookie-consent popup included on every page.

#### Rokezzz_Widget

CMS widget **Rokezzz - Baner / Zdjęcie z linkiem**: image from the media gallery, alt text and optional URL.

#### Rokezzz_Gallery

CMS widget **Rokezzz Gallery - Dynamic Carousel**: any number of images with captions from the media gallery.

#### Rokezzz_Acordeon

CMS widget **Rokezzz Accordion FAQ**: any number of heading/content rows.

#### Rokezzz_Review

Google Maps / Places reviews with a configurable cache TTL (hours). Toggle API key, place ID and limits in **Stores → Configuration → General → Rokezzz Review → Google reviews**. Magento product reviews are `Rokezzz_ProductsReview`.

#### Rokezzz_Analytics

Injects Google tracking scripts on the storefront based on admin configuration. Configure it in **Stores → Configuration → General → Rokezzz Analytics → Tracking**. Choose **GA4** or **Google tag (gtag.js)** for measurement IDs (e.g. `G-XXXXXXXXXX`) — both use the same gtag.js snippet — or **Google Tag Manager** for container IDs (`GTM-XXXXXXX`). Scripts are output only when a method is selected and the ID field is filled. GTM also adds the required `noscript` iframe right after the opening `<body>` tag.

#### Rokezzz_ChatBot

tawk.to chat widget. Toggle property / widget IDs and behaviour in **Stores → Configuration → General → Rokezzz ChatBot (tawk.to)**.

### External integrations

- **Przelewy24** — Composer package `przelewy24/magento2-przelewy24` (`PayPro_Przelewy24`), not a `Rokezzz_*` module.
- **InPost** — `Rokezzz_InPost` plus the vendor ShipX module.
- **Furgonetka** and a standalone **Waybill** module are not in this template.

## Post-installation

After installation or after enabling/disabling modules, run:

```bash
php bin/magento setup:upgrade
php bin/magento cache:flush
```

If generated files or dependency injection cache need to be rebuilt:

```bash
php bin/magento setup:di:compile
```

## Code Style

The project contains two code style configurations:

- `src/.php-cs-fixer.dist.php` - PHP-CS-Fixer configuration for `*.php` and `*.phtml` files in `src/app/code/Rokezzz`.
- `src/phpcs.xml.dist` - PHPCS configuration using `PSR12` and `Magento2` rules for `app/code/Rokezzz`.

The root `Makefile` contains helper commands. Run them from the main project directory, one level above `src` (where `compose.yaml` is located).

Check code style and show the diff:

```bash
make cs-check
```

Fix code style automatically:

```bash
make cs-fix
```

These commands use `src/vendor/bin/php-cs-fixer` and the configuration from `src/.php-cs-fixer.dist.php`.

## Unit Tests

PHPUnit tests live next to the modules in `src/app/code/Rokezzz/*/Test/Unit`. They cover domain rules (config flags, validators, extractors, eligibility) — not controllers, data patches, ResourceModels or `*Creator` wrappers.

Run them inside the PHP container (working directory is Magento `src/`):

```bash
docker compose exec php vendor/bin/phpunit --bootstrap vendor/autoload.php app/code/Rokezzz
```

To run one module:

```bash
docker compose exec php vendor/bin/phpunit --bootstrap vendor/autoload.php app/code/Rokezzz/Invoice/Test/Unit
```

### Static content deploy (`static-deploy`)

Publishes frontend and admin static assets (CSS, JS, fonts) from modules and themes into `src/pub/static`. Use this when styles or scripts return 404, or after theme or frontend changes.

```bash
make static-deploy
```

This runs `docker compose exec php php bin/magento setup:static-content:deploy pl_PL -f` inside the PHP container. The `-f` flag forces redeployment of files that already exist.

To deploy another locale:

```bash
make static-deploy LOCALE=en_US
```

After deployment, flush cache if needed:

```bash
make cache-flush
```

This runs `docker compose exec php php bin/magento cache:flush` inside the PHP container.

### Static deploy + flush (`static-refresh`)

Runs static content deploy and cache flush in one command.

```bash
make static-refresh
```

It executes `make static-deploy` followed by `make cache-flush`.

### Cache flush (`cache-flush`)

Flushes all Magento cache types from the PHP container.

```bash
make cache-flush
```

## Pull Request Checks

GitHub Actions workflow is defined in `.github/workflows/php-cs-fixer.yml`.

On pull requests, GitHub checks PHP-CS-Fixer in dry-run mode for changes in:

- `src/**/*.php`
- `src/**/*.phtml`
- `src/.php-cs-fixer.dist.php`
- `.github/workflows/php-cs-fixer.yml`

The workflow fails when code style changes are required. To block merging PRs with invalid style, enable branch protection in GitHub and require the `PHP CS Fixer` status check.
