build:
	rm -rf static
	rm -rf .env.mockapi-config .env.mockapi-external ./vendor/genesis/mock-api/.env ./vendor/genesis/mock-api/.env.external
	rm -rf Makefile
	cp ./vendor/genesis/mock-api/.env.template .env.mockapi-config
	cp ./vendor/genesis/mock-api/.env.external.template .env.mockapi-external
	ln .env.mockapi-config ./vendor/genesis/mock-api/.env
	ln .env.mockapi-external ./vendor/genesis/mock-api/.env.external
	mkdir static
	cp -R ./vendor/genesis/mock-api/sample-static/* ./static/
	echo 'up:' > Makefile
	echo '	$$(MAKE) -C ./vendor/genesis/mock-api up' >> Makefile
	echo '' >> Makefile
	echo 'down:' >> Makefile
	echo '	$$(MAKE) -C ./vendor/genesis/mock-api down' >> Makefile
	echo '' >> Makefile
	echo 'install:' >> Makefile
	echo '	$$(MAKE) -C ./vendor/genesis/mock-api install' >> Makefile
	echo '' >> Makefile
	echo 'update:' >> Makefile
	echo '	$$(MAKE) -C ./vendor/genesis/mock-api update' >> Makefile
	echo '' >> Makefile
	echo 'logs:' >> Makefile
	echo '	$$(MAKE) -C ./vendor/genesis/mock-api logs' >> Makefile
	echo '' >> Makefile
	echo 'config:' >> Makefile
	echo '	$$(MAKE) -C ./vendor/genesis/mock-api config' >> Makefile

install:
	composer install

install-ci:
	composer install
	cp -R sample-static static
	cp .env.template .env
	echo "API_MOCK_STATICS_DIR=./static" >> .env

update:
	composer update

up:
	docker-compose up -d --force-recreate mock-api

down:
	docker-compose down

logs:
	docker-compose logs -f

config:
	docker-compose config