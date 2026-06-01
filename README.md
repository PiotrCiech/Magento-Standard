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
docker compose up -d --build
```

Enter the PHP container:

```bash
docker compose exec php bash
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

## Admin Credentials

Default credentials from the example installation command:

- URL: `http://localhost:8080/admin`
- User: `admin`
- Password: `Admin123!`

## Rokezzz Modules

Custom modules are located in `src/app/code/Rokezzz`.

### Rokezzz_Tax

This module contains project tax configuration and a helper console command for product prices.

The data patch creates Polish tax rates for `0%`, `5%`, `8%` and `23%`, creates the main tax rule and saves basic tax-related store configuration, including shipping origin and tax calculation settings.


### Rokezzz_IndexerSchedule

This module sets Magento indexers to scheduled mode using a data patch. After running `setup:upgrade`, indexers are configured to work in "Update by Schedule" mode.

### Rokezzz_ReCaptcha

This module stores initial reCAPTCHA configuration for frontend and backend forms. It configures selected forms to use reCAPTCHA v3 and disables the legacy admin captcha configuration.

### Rokezzz_Compare

This module enables Magento product compare via a data patch (`rokezzz_compare/general/enabled`, `catalog/recently_products/compared_count`). Toggle it in **Stores → Configuration → General → Rokezzz Compare → Product compare → Enable product compare**. When disabled, add-to-compare links and the compare sidebar are hidden on the storefront.

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
