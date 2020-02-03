## Lumen Auth API
Provides user registration, e-mail activation, stateless authentication using signed tokens similar to JWT.

#### Requirements (non-docker start):
`php: ^7.3`, `mongodb` pecl extension, `composer`

##### Run tests (see below how to run docker-compose):
```bash
docker-compose exec php php -dopcache.enable=0 ./vendor/bin/phpunit --do-not-cache-result
```

### Start with docker:
Next steps suggest current `./docker` directory.
##### Run build docker-compose to build application:
```bash
cd docker
docker-compose -f docker-compose.build.yml up -d
``` 
 
##### Run docker-compose to run start application with dependencies: 
- Mongo (database), 
- Mailhog (mail catcher available on http://localhost:8025/)
- PHP dev server
```bash
docker-compose up -d
``` 
Wait until mongo schema is ready (mongo is not ready immediately after container start):
```bash
docker-compose exec php php artisan odm:schema:create
``` 

#### Register - creates RegistrationRequest
user1, user2, user3
```bash
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" -d 'email=user1@localhost&password=123' http://127.0.0.1:8000/register
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" -d 'email=user2@localhost&password=123' http://127.0.0.1:8000/register
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" -d 'email=user3@localhost&password=123' http://127.0.0.1:8000/register
```

#### Fill mail queue and RegistrationPending 
```bash
docker-compose exec php php artisan mail-queue:spool
```

#### Send mail queue 
```bash
docker-compose exec php php artisan mail-queue:send
```

#### Activate account 
Open mailhog `http://localhost:8025/` and navigate by activation link, response should be:
```json
{"messages":["Activated."]}
```

#### Authentication
Auth request
```bash
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" -d 'email=user3@localhost&password=123' http://127.0.0.1:8000/auth
```
should return token (contains from payload and signature joined with "~") like this:
```text
eyJlbWFpbCI6InVzZXIzQGxvY2FsaG9zdCIsImV4cCI6MTU4MDY5MzYyOH0~COSoh79OWEMl5yUnY6To7rVGTvyHUMh-1oVPdPiJXY4
```

#### Verify token
curl `http://localhost:8000/verify/%already_generated_token%`  
response: `{"messages":["Token is valid."]}`  

#### Purge all database
```bash
docker-compose exec php php artisan auth:purge
```
