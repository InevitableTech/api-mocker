build:
	rm -rf static
	rm -rf .env
	rm -rf Makefile
	cp ./vendor/genesis/api-mocker/.env.template .env
	mkdir static
	cp -R ./vendor/genesis/api-mocker/static/* ./static/
	echo 'up:' > Makefile
	echo '	$$(MAKE) -C ./vendor/genesis/api-mocker up"' >> Makefile
	echo '' >> Makefile
	echo 'down:' >> Makefile
	echo '	$$(MAKE) -C ./vendor/genesis/api-mocker down' >> Makefile
	$(MAKE) -C ./vendor/genesis/api-mocker install

install:
	composer install

up:
	docker-compose up -d mock-api

down:
	docker-compose down
