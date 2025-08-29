.PHONY: up down sh-php sh-nginx sh-mysql init symfony behat test db-wait

up:
	docker compose up -d --build

down:
	docker compose down -v

sh-php:
	docker compose exec php bash

sh-nginx:
	docker compose exec nginx sh

sh-mysql:
	docker compose exec mysql bash

db-wait:
	@echo "Waiting for MySQL health..." && sleep 2 && docker compose ps

init: up symfony behat

symfony:
	docker compose exec -u app php bash -lc '\
	  set -euo pipefail; \
	  if [ ! -f bin/console ]; then \
	    echo ">> Creating Symfony skeleton in /tmp/sf"; \
	    rm -rf /tmp/sf && mkdir -p /tmp/sf; \
	    composer create-project symfony/skeleton:^7.1 /tmp/sf; \
	    echo ">> Copying skeleton into project root"; \
	    cp -a /tmp/sf/. .; \
	  else \
	    echo ">> Symfony already present (bin/console found) — skipping skeleton"; \
	  fi; \
	  echo ">> Requiring webapp-pack, ORM, maker, doctrine-bundle, annotations"; \
	  composer require symfony/webapp-pack; \
	  composer require symfony/orm-pack; \
	  composer require --dev symfony/maker-bundle; \
	  composer require doctrine/doctrine-bundle doctrine/annotations; \
	  echo ">> Done." \
	'

test:
	docker compose exec -u app php bash -lc "APP_ENV=test ./vendor/bin/phpunit"
