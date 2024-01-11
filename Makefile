DOCKER_COMPOSE = docker-compose
EXEC_PHP       = $(DOCKER_COMPOSE) exec php-fpm
DOCKER_COMPOSE_FILE = -f docker-compose.yml

local-compose-file:
	$(eval DOCKER_COMPOSE_FILE = -f docker-compose.yml)

dev-compose-file:
	$(eval DOCKER_COMPOSE_FILE = -f docker-compose.base.yml -f docker-compose.dev.yml )

dc-build:
	$(DOCKER_COMPOSE) $(DOCKER_COMPOSE_FILE) build php-fpm www

dc-up:
	$(DOCKER_COMPOSE) $(DOCKER_COMPOSE_FILE) up -d php-fpm www

dc-down:
	$(DOCKER_COMPOSE) $(DOCKER_COMPOSE_FILE) down --remove-orphans

bash:
	$(EXEC_PHP) bash

test:
	$(EXEC_PHP) sh -c " APP_ENV=test php bin/phpunit"

composer-i:
	$(EXEC_PHP) sh -c " composer install"

clear-cache:
	$(EXEC_PHP) bash -c "rm -rf var"

nginx-restart:
	cd docker; docker exec -it www sh -c "nginx -t && nginx -s reload"

swagger-generate:
	$(EXEC_PHP) sh -c "./vendor/bin/openapi /var/www/src -o /var/www/api/openApi/swagger.json"

coverage:
	$(EXEC_PHP) sh -c " ./bin/phpunit --coverage-html coverage"
