#!/bin/bash

# Step 1: Clone the Laravel project from GitHub
git clone https://github.com/mehrparvarmoein/shab_shop.git shab_shop
cd shab_shop

# Step 2: Install Composer dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Step 3: Create a copy of the .env.example file and rename it to .env
cp .env.example .env

# Step 4: Generate the application key
php artisan key:generate

# Step 5: Run database migrations
php artisan migrate --force --seed


# Step 6: Start the Laravel development server
php artisan serve

