## Run for development
```
I expect you already have install apache, mysql5.7 and php8.1.

# Install composer
$ sudo apt install composer

# Install nvm
$ curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.3/install.sh | bash

# Install node v18.14.0(LTS)
$ nvm install v18.14.0
$ nvm alias default v18.14.0

# Install php libraries
$ composer install

# Install node modules
$ npm install

# Prepare config
$ cp .env.example .env
$ vim .env

# Run web server
$ php artisan serve

# Run vite for React development
$ npm run dev

# The open url http://localhost:8000 you can see the web page.
```

## Build for production
```
$ ./build.sh v1.0.0
# And you can see the zip file in output folder
```

## Create administrator
```
$ php artisan db:seed --class=AdminSeeder
```