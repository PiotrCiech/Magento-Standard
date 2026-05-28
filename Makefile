PHP_CS_FIXER := src/vendor/bin/php-cs-fixer
PHP_CS_FIXER_CONFIG := src/.php-cs-fixer.dist.php

LOCALE ?= pl_PL
ADMIN_STATIC_LOCALES ?= pl_PL en_US de_DE

.PHONY: cs-check cs-fix static-deploy cache-flush static-refresh admin-static-fix setup-upgrade setup-refresh

static-deploy:
	docker compose exec php php bin/magento setup:static-content:deploy $(LOCALE) -f

cache-flush:
	docker compose exec php php bin/magento cache:flush

setup-upgrade:
	docker compose exec php php bin/magento setup:upgrade

setup-refresh: cache-flush setup-upgrade

static-refresh: static-deploy cache-flush

admin-static-fix:
	docker compose exec php sh -lc "rm -rf pub/static/adminhtml/* var/view_preprocessed/* && php bin/magento setup:static-content:deploy $(ADMIN_STATIC_LOCALES) -f && php bin/magento cache:flush"

cs-check:
	$(PHP_CS_FIXER) fix --config=$(PHP_CS_FIXER_CONFIG) --dry-run --diff

cs-fix:
	$(PHP_CS_FIXER) fix --config=$(PHP_CS_FIXER_CONFIG)
