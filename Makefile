.PHONY:help
.DEFAULT_GOAL=help

COMPOSER=./composer.phar

help:
	@grep -h -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

$(COMPOSER):
	curl -sS https://getcomposer.org/installer | php -- --filename=composer.phar
	chmod +x composer.phar

vendor: $(COMPOSER) composer.json
	COMPOSER_MEMORY_LIMIT=-1 $(COMPOSER) update

cs: vendor ## Check for coding standards
	php vendor/bin/phpcs

csfix: vendor ## Check and fix for coding standards
	php vendor/bin/phpcbf

test: vendor phpunit.xml ## Unit testing
	php vendor/bin/phpunit --stop-on-error

clean: ## Remove files needed for tests
	rm -rf ./vendor
	rm -f ./composer.phar
	rm -f ./composer.lock
	rm -f .phpunit.result.cache