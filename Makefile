define makefileContent

mockapi-up:
	$(MAKE) -C ./vendor/genesis/mock-api up

mockapi-down:
	$(MAKE) -C ./vendor/genesis/mock-api down

mockapi-install:
	$(MAKE) -C ./vendor/genesis/mock-api install

mockapi-update:
	$(MAKE) -C ./vendor/genesis/mock-api update

mockapi-logs:
	$(MAKE) -C ./vendor/genesis/mock-api logs

mockapi-config:
	$(MAKE) -C ./vendor/genesis/mock-api config
endef
export makefileContent

build:
	rm -rf .env.mockapi-config ./vendor/genesis/mock-api/.env
	ln $$(pwd)/vendor/genesis/mock-api/.env .env.mockapi-config
	mkdir -p staticMocks
	cp -R ./vendor/genesis/mock-api/sample-static/* ./staticMocks/
	echo 'API_MOCK_STATICS_DIR=./../../../staticMocks' >> .env.mockapi-config
	echo "$$makefileContent" >> Makefile
	echo '[SUCCESS]';

install:
	composer install

install-ci:
	composer install
	cp -R sample-static static
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