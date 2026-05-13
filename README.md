Rental platform for managing multiple types of resources (cars, books, movies) using Laravel, MySQL and Docker.


## How to run

1. Clone repo
   `git clone https://github.com/maciek-wroblewski/multi-rental-platform.git`

2. Install dependencies
   `composer install`

3. Configure environment
   `cp .env.example .env`

4. Start Docker
   `docker compose up -d`

5. Run migrations
   `docker compose exec laravel.test php artisan migrate`
   
