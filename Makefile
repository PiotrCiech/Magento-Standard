PHP_CS_FIXER := src/vendor/bin/php-cs-fixer
PHP_CS_FIXER_CONFIG := src/.php-cs-fixer.dist.php

.PHONY: cs-check cs-fix

cs-check:
	$(PHP_CS_FIXER) fix --config=$(PHP_CS_FIXER_CONFIG) --dry-run --diff

cs-fix:
	$(PHP_CS_FIXER) fix --config=$(PHP_CS_FIXER_CONFIG)
