# Customers API (Laravel)

## Project's dependencies:
- [Docker](https://www.docker.com/)
- [Composer](https://getcomposer.org/)

## Installation & run

1. Clone the repo and run:

```
cp .env.example .env
```

2. Install Laravel's dependencies:
```
composer install
```

3. Start the containers:

```
./vendor/bin/sail up -d
```

4. Generate the app key:
```
./vendor/bin/sail artisan key:generate
```

5. Run the migrations:
```
./vendor/bin/sail artisan migrate
```

6. Run the seeder:
```
./vendor/bin/sail artisan db:seed
```

The API will be available at `http://localhost`

## Admin user
After running the seeder, the admin user will be created with these credentials
```
email: admin@admin.com
password: 123456
```

## API
To make requests to the API, you'll need a JWT token. To get your token, follow these steps:

1) Make a `POST` request to the login endpoint:
```
POST /api/auth/login

Headers:
{
    Accept: application/json
}

Request body:
{
	"email": "admin@admin.com",
	"password": "123456"
}
```

If the email and password are correct, you'll reveice a JSON response containing your token:
```
{
    "token": "XXXXXXXXXXXXXXXXXXXXX"
}
```

2) For every other request you need to send this token in the header:
```
Headers: {
    Authorization: Bearer <YOUR TOKEN>
}
```

## Documentation
The full API documentation (Swagger) is available at `http://localhost/api/documentation`

## Test
To test the applicaition, run the following command:
```
./vendor/bin/sail artisan test
```
