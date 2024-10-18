## Authors
- [@Ahmed safwat](https://github.com/AhmedSafwat1)

## Overview
- use jwt to generate token
- use omnipay and paypal for payment 
- use spatie/laravel-permission for permissions
- pusher/pusher-php-server for realtime notifications
- provide api for login to generate token
- provide api for create order 
- provide api for change status for order ( also did create table contain any changes do in order to kept old and new value)
- three api to handle payment  complete , cancel  and webhook

## Requirements
- php version >=8.0
- mysql database

## Setting up the project locally.
- Install
```bash
  composer i
```

- Handle  .env

```bash
# Change the value
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```
- Run Seeder 
```bash
php artisan db:seed
```

- Create jwt secret
```bash
php artisan jwt:secret
```

- Now can use this account in api login to get jwt token this user have role admin and have all permission which need
```
Email    : test@test.com
Password : test123456
```
# Configuring the payment gateway integration. ( i use PAYPAL by Omnipay pakcage)

```bash
PAYPAL_CLIENT_ID="AaxDq2YIjqz9b-J4Xandmi-NtWx2z-wh4j4b-mW7vPBoawX3k_dIV7UsojyXDlvUMtjuh0sU3CB5pLOff"
PAYPAL_SECRET="EGiR70bdZQ-Pjueoj1Qt6OjqL48NbdI7OSMZegMAuA_RngSgNEFwae4iJTE8JB8HNJ246f8pF4RM24JE"
PAYPAL_TEST_MODE=true
```

## Run Unit test 

```bash
 php artisan test

```

## Configuring Real-Time Notifications (Use Pusher)
```bash
PUSHER_APP_ID=1882066
PUSHER_APP_KEY=7c51c6dd1e28a98310d7
PUSHER_APP_SECRET=7f587588bee4a24ce4a1
PUSHER_APP_CLUSTER="eu"
PUSHER_SCHEME="https"
```

## API Documentation (Postman collection)
- document https://documenter.getpostman.com/view/6461163/2sAXxWYoWb
- collection Find in project with name `Velents-Task.postman_collection.json`

## Testing report with coverage details.
-  write some unit and create coverage test for but not cover all code 
-  can find detail in folder coverage
-  coverage around 80 %

## CI/CD Pipeline
- do to run unit test 
- run ./vendor/bin/pint to format code 
- run ./vendor/bin/phpstan analyse to analyse the code