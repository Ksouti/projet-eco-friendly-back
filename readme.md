# Eco-friendly back (A adapter au backend)

## What are the requirements?

PHP 7.4.3 (minimum)

## How to install the project?

1. Clone project

`git clone git@github.com:O-clock-Lara/projet-11-eco-friendly-back.git`

2. Move in the project

`cd projet-11-eco-friendly-back`

3. Installing dependencies

`composer install`

4. Configure a database and fill in the `.env` file

`DATABASE_URL="<db_type>://<username>:<password>@127.0.0.1:3306/<db_name>?serverVersion=<server_version>"`

5. Run migration

`php bin/console make:migration`
`php bin/console doctrine:make:migrations`

6. Install CKEditor

`php bin/console ckeditor:install`
`php bin/console assets:install`

7. (optionnal) Load fixtures

`php bin/console doctrine:fixtures:load`

## What command can I use for in project?

1. Start a development server

From the project's root: `php -S 0.0.0.0:8000 -t public`
