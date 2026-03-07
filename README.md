## OAuth2 Identity Provider
A lightweight, Clean Architecture implementation of an OAuth2 Identity Provider using Slim 4, Doctrine DBAL, and Lcobucci JWT with RS256 asymmetric signing.

## Installation
```bash
composer install

#generate rsa keys
mkdir -p var/keys
openssl genrsa -out var/keys/private.pem 2048
openssl rsa -in var/keys/private.pem -pubout -out var/keys/public.pem

#db
# Create the sqlite database and run migrations
vendor/bin/phinx migrate

# Seed a test client (client_id: mobile-app, secret: super-secret-123)
vendor/bin/phinx seed:run

#run server
php -S localhost:8080 -t public

testing/ request a token
curl -X POST http://localhost:8080/auth/token \
     -d "client_id=mobile-app" \
     -d "client_secret=super-secret-123"
     
#tests
vendor/bin/phpunit --testdox tests
```