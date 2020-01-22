# gush.co

## pre-setup

### database for local machine
- make sure mysql is installed (using v5.7)
- install mongo driver from the [official page](https://docs.mongodb.com/ecosystem/drivers/php/#installation)

### web server for local machine
- install [valet](https://laravel.com/docs/6.x/valet) if you are using mac
- install [mamp](https://www.mamp.info/en/) or [homestead](https://laravel.com/docs/6.x/homestead) if you are using windows

## setup for dev
```
git clone git@github.com:devopsgc/gush.git
cd gush
cp .env.example .env
```

- create a new database in mysql (by default is gushcloud)
- configure .env file with the correct db information

```
composer install
php artisan key:generate
php artisan db:refresh --seed
```

- go to http://gush.test (if you are using valet) or http://localhost/gush/public (if you are using mamp)
- if you now see the login page, setup is successful

## others to note
- setup mail credentials in .env with a [mailtrap](https://mailtrap.io/inboxes) account
- setup other .env configurations as needed during development (eg. social media credentials)
- use mongoDB Compass Community Edition for viewing the mongo being run on the local machine
- install [ide helper](https://github.com/barryvdh/laravel-ide-helper) for linting help on IDE such as visual studio code if needed
- install js dependencies (only needed if there are changes)
```
npm install
```
- build js production (only needed if there are changes)
```
npm run prod
```

## Testing
- run all tests for php code
```
vendor/phpunit/phpunit/phpunit
```
- run test for one function or class
```
vendor/phpunit/phpunit/phpunit --filter <function_name>
```
