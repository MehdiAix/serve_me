.PHONY: install update clean help test dusk dev stop-dev build migrate bash lint-js lint-php
.DEFAULT_GOAL   = help

include .env

PRIMARY_COLOR   		= \033[0;34m
PRIMARY_COLOR_BOLD   	= \033[1;34m
SUCCESS_COLOR   		= \033[0;32m
SUCCESS_COLOR_BOLD   	= \033[1;32m
DANGER_COLOR    		= \033[0;31m
DANGER_COLOR_BOLD    	= \033[1;31m
WARNING_COLOR   		= \033[0;33m
WARNING_COLOR_BOLD   	= \033[1;33m
NO_COLOR      			= \033[m

# For test
filter      ?= tests
dir         ?=

php_test := docker-compose -f docker-compose-test.yaml exec php php
mariadb_test := docker-compose -f docker-compose-test.yaml exec mariadb mysql -psecret -e

php := docker-compose run --rm php php
mariadb := docker-compose exec mariadb mysql -p -e
bash := docker-compose run --rm php zsh
composer := docker-compose run --rm php composer


vendor: composer.json
	@$(composer) install

install: vendor  ## Install the composer dependencies

update: ## Update the composer dependencies
	@$(composer) update

clean: ## Remove cache
	@echo "$(DANGER_COLOR)Clearing Symfony cache...$(NO_COLOR)"
	@$(php) bin/console cache:pool:clear --quiet cache.app
	@echo "$(DANGER_COLOR)Removing billings PDF...$(NO_COLOR)"
	@rm -rf ./var/storage/billings/*

help: ## Display this help
	@awk 'BEGIN {FS = ":.*##"; } /^[a-zA-Z_-]+:.*?##/ { printf "$(PRIMARY_COLOR_BOLD)%-15s$(NO_COLOR) %s\n", $$1, $$2 }' $(MAKEFILE_LIST) | sort

test: ## Run PHP tests (parameters : dir=tests/Feature/LoginTest.php || filter=get)
	@docker-compose -f docker-compose-test.yaml up -d
	@$(mariadb_test) "drop database if exists $(APP_NAME)_test; create database $(APP_NAME)_test;"
	@$(php_test) bin/phpunit $(dir) --filter $(filter) --testdox
	@docker-compose -f docker-compose-test.yaml down

start-dev: ## Run development servers
	@docker-compose up -d
	@echo "Dev server launched on http://127.0.0.1:$(DOCKER_APP_PORT)"
	@echo "Mail server launched on http://127.0.0.1:1025"
	@docker exec -it docker bash

stop-dev: ## Stop development servers
	@docker-compose down
	@echo "Dev server stopped: http://localhost:$(DOCKER_APP_PORT)"
	@echo "Mail server stopped: http://localhost:1025"

stop-build:
	@docker-compose down

start-build:
	@docker-compose build

build: install ## Build assets projects for production
	@rm -rf ./public/assets/*

migrate: clean ## Refresh database by running new migrations
	@echo "$(PRIMARY_COLOR)Migrating database...$(NO_COLOR)"
	@$(php) bin/console doctrine:migrations:migrate --no-interaction --quiet
	@$(php) bin/console doctrine:fixtures:load --no-interaction --no-debug

purge-database: ## Purge dev database (CLEAN_MIGRATIONS=0[default] : remove migrations and make:migration)
	@$(mariadb) "drop database if exists $(APP_NAME); create database $(APP_NAME);"
ifdef CLEAN_MIGRATIONS
	@rm -rf migrations/*
	@$(php) bin/console make:migration
endif

bash: ## Run bash in PHP container
	@$(bash)

lint-php: ## Lint PHP
	@$(php) -d memory_limit=-1 vendor/bin/phpstan analyze
