up:
	docker compose up -d
stop:
	docker compose stop
down:
	docker compose down
build:
	docker compose build
destroy:
	docker compose down --volumes --remove-orphans
destroy-all:
	docker compose down --rmi all --volumes --remove-orphans
remake:
	@make down
	@make install
	@make connect-db
	@make migrate
	@make seed
	@make cache
	@make phpcs
install:
	@make build
	@make up
	@make composer
restart:
	@make down
	@make up
ps:
	docker compose ps
logs:
	docker compose logs
mysql:
	docker compose exec mysql bash
test:
	docker compose exec -e APP_ENV=testing app bash -c 'php artisan test $(TEAMCITY_REPORT)'
composer:
	docker compose exec app bash -c 'composer install'
generate:
	docker compose exec app bash -c 'php artisan key:generate'
migrate:
	docker compose exec app bash -c 'php artisan migrate --force'
seed:
	docker compose exec app bash -c 'php artisan db:seed --force'
cache:
	docker compose exec app bash -c 'php artisan cache:clear'
init:
	cp -f ./docker-compose.dev.yml ./docker-compose.yml
	cp -f ./.env.example ./.env
	make remake
	make composer
	make generate
connect-db:
	docker compose exec app bash -c 'php artisan connect:db'
phpcs:
	docker compose exec app bash -c 'vendor/bin/phpcs$(TEAMCITY_PHPCS_REPORT)'
phpcbf:
	docker compose exec app bash -c 'vendor/bin/phpcbf'
phpcs-fix:
	@make phpcbf
	@make phpcs
