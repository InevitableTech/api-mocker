build:
	rm -rf static
	rm -rf .env
	rm -rf Makefile
	cp ./vendor/genesis/mock-api/.env.template .env
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

install:
	composer install

update:
	composer update

up:
	docker-compose up -d mock-api

down:
	docker-compose down

logs:
	docker-compose logs -f
