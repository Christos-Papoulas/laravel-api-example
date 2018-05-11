# Social Network API

## Deployment

```
composer install
php artisan key:generate
php artisan jwt:secret
```


## Run tests

```
vendor/bin/phpunit
```

## Generate API documentation

If you generating the docs for the first time run the following:
- `npm install`
- `cp apidoc.json.example apidoc.json`
- edit the apidoc.json and add the appropriate values.

If you have configure the apidocjs just run the following:
```
apidoc -i app/ -o public/apidoc/
```

Visit: <URL>/apidoc/index.html


## Feature list

- Authentication: Login, Register, Password Reset