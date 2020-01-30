## Lumen Auth API

#### Requirements (non-docker start):
`php: ^7.3`, `mongodb` pecl extension, `composer`
##### Run tests (see below how to run docker-compose):
```bash
docker-compose exec php php -dopcache.enable=0 ./vendor/bin/phpunit --do-not-cache-result
```

### Local setup (using docker):
Next steps suggest current `./docker` directory.
##### Run build docker-compose to build application:
```bash
cd docker
docker-compose -f docker-compose.build.yml up
``` 
 
##### Run docker-compose to run start application with dependencies: 
- Mongo (database), 
- Mailhog (mail catcher available on http://localhost:8025/)
- PHP dev server
```bash
docker-compose up -d
``` 

#### Register - creates RegistrationRequest
user1, user2, user3
```bash
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" -d 'email=user1@localhost&password=123' http://127.0.0.1:8000/auth
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" -d 'email=user2@localhost&password=123' http://127.0.0.1:8000/auth
curl -X POST -H "Content-Type: application/x-www-form-urlencoded" -d 'email=user3@localhost&password=123' http://127.0.0.1:8000/auth
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
Open email (navigate to mailhog http://localhost:8025/) and navigate by activation link, response should be:
```json
{"messages":["Activated."]}
```

#### Authentication
Auth request
```bash
curl -v -X POST -H "Content-Type: application/x-www-form-urlencoded" -d 'email=user1@localhost&password=123' http://127.0.0.1:8000/auth
```
should return token like this:
```text
{"signature":"$argon2id$v=19$m=65536,t=4,p=1$SVBYZlhvQmdhcnFDbGdDZg$wG6MvHxDakXXKOrfRPiXWiArxnJPviYa25osf+zfmdg","expires":1580346332,"email":"user1@localhost"}
```

#### Verify token
Simply navigate to `http://localhost:8000/verify/%already_generated_token%`  
Should return: `{"messages":["Token is valid."]}`  
P.S. Token should be url-encoded: `encodeURIComponent(%token%)` for JS.

#### Purge add database
```bash
docker-compose exec php php artisan auth:purge
```

## TODO:
- Add Doctrine ODM to console.
- Run `odm:schema:create`
