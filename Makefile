MAGENTO_DIR := src
PHP_CS_FIXER := vendor/bin/php-cs-fixer
PHP_CS_FIXER_CONFIG := .php-cs-fixer.dist.php

LOCALE ?= en_US
STATIC_LOCALES ?= pl_PL en_US de_DE
ADMIN_STATIC_LOCALES ?= $(STATIC_LOCALES)

.PHONY: fix cs-check cs-fix static-deploy cache-flush static-refresh admin-static-fix setup-upgrade setup-refresh static-clean static-fresh

fix:
	docker compose exec php sh -lc "rm -rf pub/static/adminhtml/* pub/static/frontend/* var/view_preprocessed/* && php bin/magento setup:static-content:deploy $(STATIC_LOCALES) -f && php bin/magento cache:flush"

static-deploy:
	docker compose exec php sh -lc "rm -rf pub/static/frontend/Rokezzz/default/$(LOCALE)/Rokezzz_CatalogListing var/view_preprocessed/pub/static/frontend/Rokezzz/default/$(LOCALE)/Rokezzz_CatalogListing && php bin/magento setup:static-content:deploy $(LOCALE) -f --theme Rokezzz/default"

static-clean:
	docker compose exec php sh -lc "rm -rf pub/static/frontend/*/$(LOCALE)/Rokezzz_Orders var/view_preprocessed/pub/static/frontend"

static-fresh: static-clean static-deploy cache-flush

cache-flush:
	docker compose exec php php bin/magento cache:flush

setup-upgrade:
	docker compose exec php php bin/magento setup:upgrade

static-refresh: static-deploy cache-flush

admin-static-fix: fix

admin-setup-refresh: cache-flush setup-upgrade admin-static-fix

cs-check:
	cd $(MAGENTO_DIR) && $(PHP_CS_FIXER) fix --config=$(PHP_CS_FIXER_CONFIG) --dry-run --diff

cs-fix:
	cd $(MAGENTO_DIR) && $(PHP_CS_FIXER) fix --config=$(PHP_CS_FIXER_CONFIG)
