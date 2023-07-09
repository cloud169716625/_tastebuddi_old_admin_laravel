# TravelBuddi Backend

## Prerequisites
- PHP 7.2.x
- Composer 1.7.2
- NodeJS - node v8.x.x (npm v5.x.x) or Higher

## Getting Started
- Copy `.env.example` to `.env`
- Run `composer install`
- Run `php artisan jwt:secret` to create JWT Secret key
- Run `php artisan key:generate` to create application key
- Run `php artisan migrate` to execute outstanding migrations
- Run `php artisan storage:link` to create a symlink from public/storage to storage/app/public (video uploads)
- Run `php artisan db:seed`


## Initial DB design

- https://app.sqldbm.com/MySQL/Share/EOanL7O2KkasJe3cms6LPkGFrngIE8md_DYjF4jNYw0
