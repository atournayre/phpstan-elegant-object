# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc
YELLOW=\033[33m
RESET=\033[0m

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	composer $(c)

composer-update-dry-run: ## Run composer update --dry-run, pass the parameter "c=" to run a given command, example: make composer-update-dry-run c='req symfony/orm-pack'
	@$(eval c ?=)
	composer update --dry-run $(c)

composer-update: ## Run composer update --dry-run, pass the parameter "c=" to run a given command, example: make composer-update-dry-run c='req symfony/orm-pack'
	@$(eval c ?=)
	composer update $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install
vendor: composer

## —— QA 🏆 ————————————————————————————————————————————————————————————————————
phpstan: ## Analyse code with PHPStan
	@echo "${YELLOW}PHPStan${RESET}"
	php vendor/bin/phpstan --version
	php vendor/bin/phpstan

## —— Tests 🧪 ———————————————————————————————————————————————————————————————————
phpunit: ## Runs unit tests via phpunit (pure PHP)
	@echo "${YELLOW}PHPUnit${RESET}"
	php vendor/bin/phpunit
