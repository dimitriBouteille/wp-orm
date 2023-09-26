.PHONY: csFixer runPHPStan

# Fix CS fixer
csFixer:
	php vendor/bin/php-cs-fixer fix

# Run phpStan check
runPHPStan:
	vendor/bin/phpstan analyse -c phpstan.neon