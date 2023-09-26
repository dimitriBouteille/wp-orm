.PHONY: csFixer runPHPStan

# Fix CS fixer
csFixer:
	php vendor/bin/php-cs-fixer fix

runPHPStan:
	vendor/bin/phpstan analyse -c phpstan.neon