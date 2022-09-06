
start: serve

serve: stop build
	@docker-compose build php
	@docker-compose up -d --force-recreate
	@echo "Visit: <http://localhost:8080>"

build:
	@docker-compose build php

stop:
	@docker stop $$(docker ps | grep ":8080" | cut -c1-12) > /dev/null 2>&1 || true

install:
	@docker-compose run --rm php composer install

php-imap2:
	@docker-compose run --rm php composer require javanile/php-imap2:0.1.8

release:
	# Release 1
	git add .
	git commit -am "Release"
	git push
	git push heroku main