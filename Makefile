.PHONY: help up down install test test-coverage lint lint-fix autoload restart logs shell

help:
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Targets:'
	@egrep '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose restart

logs:
	docker compose logs -f

shell:
	docker compose exec app bash

install: up
	docker compose exec app composer install

test:
	docker compose exec app composer test

test-coverage:
	docker compose exec app composer test-coverage

lint:
	docker compose exec app composer lint

lint-fix:
	docker compose exec app composer lint:fix

autoload:
	docker compose exec app composer dump-autoload

fresh: down up install

setup: fresh test

check: lint test
