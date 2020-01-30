# Run Lumen Auth API

#### For dev env:
##### Run docker containers: 
- Mongo (database), 
- Mailhog (mail catcher available on http://localhost:8025/)
```bash
cd docker
docker-composer up -d
``` 
- Run local PHP server 
```bash
php -S 127.0.0.1:8000 -t public
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
php artisan mail-queue:spool
```

#### Send mail queue 
```bash
php artisan mail-queue:send
```

#### Activate account 
Open email and navigate by activation link, response should be:
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
Should return: `{"messages":["Token is invalid."]}`
