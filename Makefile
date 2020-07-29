build:
	rm -rf .env.mockapi-config .env.mockapi-external ./vendor/genesis/mock-api/.env ./vendor/genesis/mock-api/.env.external
	cp ./vendor/genesis/mock-api/.env.template .env.mockapi-config
	cp ./vendor/genesis/mock-api/.env.external.template .env.mockapi-external
	ln .env.mockapi-config ./vendor/genesis/mock-api/.env
	ln .env.mockapi-external ./vendor/genesis/mock-api/.env.external
	mkdir -p staticMocks
	cp -R ./vendor/genesis/mock-api/sample-static/* ./staticMocks/
	echo '' >> Makefile
	echo 'mockapi-up:' >> Makefile
	echo '	$$(MAKE) -C ./vendor/genesis/mock-api up' >> Makefile
	echo '' >> Makefile
	echo 'mockapi-down:' >> Makefile
	echo '	$$(MAKE) -C ./vendor/genesis/mock-api down' >> Makefile
	echo '' >> Makefile
	echo 'mockapi-install:' >> Makefile
	echo '	$$(MAKE) -C ./vendor/genesis/mock-api install' >> Makefile
	echo '' >> Makefile
	echo 'mockapi-update:' >> Makefile
	echo '	$$(MAKE) -C ./vendor/genesis/mock-api update' >> Makefile
	echo '' >> Makefile
	echo 'mockapi-logs:' >> Makefile
	echo '	$$(MAKE) -C ./vendor/genesis/mock-api logs' >> Makefile
	echo '' >> Makefile
	echo 'mockapi-config:' >> Makefile
	echo '	$$(MAKE) -C ./vendor/genesis/mock-api config' >> Makefile

install:
	composer install

install-ci:
	composer install
	cp -R sample-static static
	cp .env.template .env
	cp .env.external.template .env.external
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