# Create a web service exposing an API

Project number seven completed as part of my OpenClassrooms training.

### Requirements

 * PHP 8.1
 * Symfony CLI
 * Composer 2.3
 
## Install

1. In your terminal, execute the following command to clone the project into the "blog" directory.
```shell
git clone https://github.com/damien-jandard/create-a-web-service-exposing-an-api.git bilemo
```

2. Access the "bilemo" directory.
```shell
cd bilemo
```

3. Duplicate and rename the .env file to .env.local, and modify the necessary information (APP_ENV, APP_SECRET, DATABASE_URL, JWT_PASSPHRASE).
```shell
cp .env .env.local
```

4. Install the composer dependencies.
```shell
composer install
```

5. Generate the private and public keys using the JWT_PASSPHRASE defined in step 3.
```shell
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
```
```shell
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

6. Create the database.
```shell
symfony console doctrine:database:create
```

7. Run the migrations.
```shell
symfony console doctrine:migration:migrate --no-interaction
```

7. Adding default fixtures.
```shell
symfony console doctrine:fixtures:load --no-interaction
```

7. Start the local server.
```shell
symfony server:start -d
```

8. You can test the API via the URL `/api/doc` or with [Postman](https://www.postman.com/downloads/) using the following credentials.

- User Account:
	- Username: `user@sprint.com`
	- Password: `User1234*`
