PHP_CS_FIXER := src/vendor/bin/php-cs-fixer
PHP_CS_FIXER_CONFIG := src/.php-cs-fixer.dist.php

LOCALE ?= pl_PL

.PHONY: cs-check cs-fix static-deploy cache-flush static-refresh

static-deploy:
	docker compose exec php php bin/magento setup:static-content:deploy $(LOCALE) -f

cache-flush:
	docker compose exec php php bin/magento cache:flush

static-refresh: static-deploy cache-flush

cs-check:
	$(PHP_CS_FIXER) fix --config=$(PHP_CS_FIXER_CONFIG) --dry-run --diff

cs-fix:
	$(PHP_CS_FIXER) fix --config=$(PHP_CS_FIXER_CONFIG)
