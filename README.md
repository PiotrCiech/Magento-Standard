# Template Magento

Empty Magento 2.4.9 project prepared as a base for Rokezzz Magento projects.

The project contains a Docker-based local environment, basic Magento infrastructure services, custom Rokezzz modules and code style tooling for PHP files in `app/code/Rokezzz`.

Main project assumptions:

- Magento source code is kept in the `src` directory.
- Custom project modules are kept in `src/app/code/Rokezzz`.
- Local environment is managed by `compose.yaml`.
- PHP code style is checked by PHP-CS-Fixer and PHPCS.
- Pull requests are checked by GitHub Actions.

## Getting started (nowi w projekcie)

Krok po kroku, żeby odpalić projekt lokalnie (Windows + WSL2 + Docker Desktop).

### 1. Wymagania

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) — włączony backend **WSL 2**
- [WSL 2](https://learn.microsoft.com/windows/wsl/install) z dystrybucją Ubuntu
- Git (w WSL: `sudo apt update && sudo apt install -y git`)
- Konto GitHub z dostępem do organizacji **rokezzz** (poproś kogoś z zespołu o zaproszenie do repo)
- **Access Keys** ze wspólnego konta firmowego Magento Marketplace (z menedżera haseł / od kogoś z zespołu) — patrz krok 7

### 2. Docker Desktop

1. Zainstaluj Docker Desktop i uruchom go.
2. Settings → **Resources** → **WSL Integration** → włącz integrację z Ubuntu.
3. Sprawdź w terminalu WSL:

```bash
docker version
docker compose version
```

Jeśli komendy nie działają, Docker Desktop nie działa albo integracja WSL jest wyłączona.

### 3. Dostęp do GitHuba (SSH)

W WSL wygeneruj klucz (jeśli jeszcze nie masz) i dodaj go w GitHub → Settings → SSH and GPG keys:

```bash
ssh-keygen -t ed25519 -C "twoj@email.pl"
cat ~/.ssh/id_ed25519.pub
```

Test połączenia:

```bash
ssh -T git@github.com
```

Powinno pojawić się powitanie z nazwą użytkownika GitHub.

### 4. Sklonuj repozytorium

```bash
cd ~/rokezzz
git clone git@github.com:rokezzz/template-magento.git
cd template-magento
git checkout develop
```

Praca idzie na branchu `develop` (feature branche od niego).

### 5. Konfiguracja `.env`

```bash
cp .env.example .env
```

Ustaw `HOST_UID` / `HOST_GID` na wartości z WSL (`id -u` / `id -g`), żeby pliki w `src` miały poprawne uprawnienia. Hasła i porty z `.env.example` zwykle wystarczą lokalnie.

**Ważne:** `.env` / `.env.example` konfigurują **Docker** (MariaDB, Valkey, RabbitMQ, porty). To **nie** jest instalacja Magento i **nie** zastępuje `setup:install`. Hasła z `.env` muszą się zgadzać z tymi podanymi przy `setup:install` (albo z gotowym `app/etc/env.php` + dumpem bazy).

### 6. Start środowiska Docker

Z katalogu głównego projektu (tam gdzie jest `compose.yaml`):

```bash
docker compose up -d --build
```

Pierwszy build może potrwać dłużej.

### 7. Composer / Magento Marketplace

`src/vendor/` nie jest w repo — paczki ściąga Composer z `repo.magento.com`.

#### Jak robimy to w zespole (żeby nikt nie kupował modułów osobno)

Magento wiąże paczki Marketplace z **kontem**, nie z ludźmi. Dlatego:

1. Jest **jedno wspólne konto firmowe** na [marketplace.magento.com](https://marketplace.magento.com/), na którym raz kupione / pobrane są wszystkie rozszerzenia z `src/composer.json` (InPost, Przelewy24, Furgonetka itd.).
2. Na tym koncie są **Access Keys** (Public + Private).
3. Klucze leżą w **menedżerze haseł zespołu** (1Password / Bitwarden / …) — **nie w repozytorium Git**.
4. Nowa osoba dostaje tylko te klucze od zespołu i odpala Composer. **Nie musi** wchodzić na Marketplace i nic kupować.

Nowy developer:

```bash
docker compose exec php bash
composer config --auth http-basic.repo.magento.com PUBLIC_KEY PRIVATE_KEY
composer install
```

`PUBLIC_KEY` = username, `PRIVATE_KEY` = password. Powstaje `src/auth.json` (jest w `.gitignore` — nigdy go nie commituj).

Gdy dokładacie **nowy** moduł z Marketplace: jedna osoba loguje się na konto firmowe, robi Get Access / zakup, aktualizuje `composer.json` / lock — reszta tylko `composer install` na tych samych kluczach.

#### Kiedy powstaje 401 / 404

| HTTP | Znaczenie | Co zrobić |
| --- | --- | --- |
| **401** Invalid credentials | Złe / zamienione klucze | Weź świeże Access Keys z konta firmowego (Public ≠ Private) |
| **404** file could not be downloaded | Na koncie firmowym brakuje entitlement do tej paczki | Na koncie firmowym dodaj brakujący produkt do My Purchases; nie zakładaj własnego konta „na próbę” |

Nie rozwiązaniem jest wrzucanie `vendor/` do Gita ani rozdawanie zipów — to łamie licencje Marketplace i puchnie repo.

### 8. Instalacja Magento i codzienna praca

Po `composer install`:

1. **Pierwszy raz na maszynie** (brak `src/app/etc/env.php`) → [First Install](#first-install) (`setup:install`). To tworzy `env.php` i schemat w bazie.
2. **Potem** (oraz po każdym pullu z nowymi modułami / schemą) → `setup:upgrade`:

```bash
docker compose exec -u www-data php php bin/magento setup:upgrade
```

Albo skrót z Makefile (to samo):

```bash
make setup-upgrade
make cache-flush
```

- storefront: http://localhost:8080/
- admin: http://localhost:8080/admin (domyślnie `admin` / `Admin123!` po First Install)
- Mailhog: http://localhost:8025/

Inne komendy:

```bash
docker compose up -d          # start
docker compose exec php bash  # shell w PHP (workdir = src)
make restart                  # gdy Varnish zwraca 503 Backend fetch failed
```

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
docker compose up -d --build
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
docker compose up -d --build
```

## First Install

Tylko gdy Magento **nie jest jeszcze zainstalowane** na tej maszynie (brak `src/app/etc/env.php`).  
`setup:upgrade` **nie** zastępuje tej komendy.

Hasła poniżej muszą być spójne z `.env` (DB: `magento`/`magento`; Valkey/RabbitMQ jak w `.env.example`).

Z hosta (katalog projektu):

```bash
docker compose exec -u www-data php php bin/magento setup:install \
    --cleanup-database \
    --base-url=http://localhost:8080/ \
    --db-host=db \
    --db-name=magento \
    --db-user=magento \
    --db-password=magento \
    --admin-firstname=Admin \
    --admin-lastname=User \
    --admin-email=admin@rokezzz.pl \
    --admin-user=admin \
    --admin-password='Admin123!@#' \
    --language=pl_PL \
    --currency=PLN \
    --timezone=Europe/Warsaw \
    --use-rewrites=1 \
    --backend-frontname=admin \
    --search-engine=opensearch \
    --opensearch-host=opensearch \
    --opensearch-port=9200
```

Nie dodawajcie `--disable-modules=Magento_InventorySales` — wyłącza MSI w trakcie install i pada na reindexie (`GetIsStockItemSalableConditionInterface`).

Po udanym install:

```bash
docker compose exec -u www-data php php bin/magento setup:upgrade
docker compose exec -u www-data php php bin/magento cache:flush
make restart
```

Opcjonalnie Redis (Valkey) / RabbitMQ możecie skonfigurować po install albo dopisać flagi `--cache-backend=redis` / `--amqp-*` do `setup:install` — **bez** wyłączania InventorySales.

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
### Rokezzz_Review

This module enables Magento core product reviews via a data patch (`catalog/review/active`, `catalog/review/allow_guest`). After `setup:upgrade`, reviews are available on the product page. Toggle them in **Stores → Configuration → General → Rokezzz Reviews → Opinie produktowe**. New reviews are **Pending** until approved in **Marketing → User Content → All Reviews**.

### Rokezzz_Wishlist

This module enables Magento core wishlist (schowek) via a data patch (`wishlist/general/active`). Toggle it in **Stores → Configuration → General → Rokezzz Wishlist → Schowek → Enable wishlist**. When disabled, wishlist UI and controllers are blocked on the storefront.

### Rokezzz_AvailabilityNotification

This module wraps Magento core back-in-stock alerts (`catalog/productalert/allow_stock`). After `setup:upgrade`, availability notifications are **disabled** by default. Toggle them in **Stores → Configuration → General → Rokezzz Availability Notification → Product availability alerts → Enable availability notifications**. When disabled, the subscribe form on out-of-stock product pages is hidden and direct subscription requests are blocked on the storefront.

### Rokezzz_OrderOwnerNotification

This module sends an optional email to the store owner after each new order is placed. Toggle it in **Stores → Configuration → General → Rokezzz Order Owner Notification → Store owner order notification** (disabled by default); set the recipient address or leave it empty to use the store general contact email.

### Rokezzz_ViteDev

Development-only bridge to the Vite server (`vite/`, Docker service `vite`). Injects Vite scripts on the storefront when port `5173` is up; register edited SCSS/LESS in `vite/src/frontend.scss`. Disable in production.

### Rokezzz_InPost

Configure InPost in **Stores → Configuration → General → Rokezzz InPost → Enable InPost integration** (default: disabled), set ShipX credentials in **Sales → Shipping Settings → InPost**, and enable delivery methods in **Sales → Delivery Methods**.

### Rokezzz_Analytics

This module injects Google tracking scripts on the storefront based on admin configuration. Configure it in **Stores → Configuration → General → Rokezzz Analytics → Tracking**. Choose **GA4** or **Google tag (gtag.js)** for measurement IDs (e.g. `G-XXXXXXXXXX`) — both use the same gtag.js snippet — or **Google Tag Manager** for container IDs (`GTM-XXXXXXX`). Scripts are output only when a method is selected and the ID field is filled. GTM also adds the required `noscript` iframe right after the opening `<body>` tag.

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
