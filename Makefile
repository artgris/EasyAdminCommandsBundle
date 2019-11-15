CONSOLE = php bin/console

fix:
	php -d memory_limit=1024m vendor/bin/php-cs-fixer fix -v