# News Aggregator API

A Laravel-based RESTful API for a news aggregator service that collects articles from various sources, with support for user preferences, pagination, and search functionality.

## Features

- User Authentication (registration, login, and logout)
- Article Management (search by category, keyword, etc.)
- User Preferences for personalized news feed
- Data aggregation from RSS sources
- Rate limiting and caching using Redis
- API documentation with Swagger/OpenAPI

## Table of Contents

1. [Requirements](#requirements)
2. [Setup Instructions](#setup-instructions)
3. [Running the Docker Environment](#running-the-docker-environment)
4. [API Documentation](#api-documentation)
5. [Additional Notes](#additional-notes)

## Requirements

- Docker
- Docker Compose
- Composer (for initial Laravel setup)
- Laravel 11

1. **Clone the Repository**

   ```bash
   git clone https://github.com/aliarshaddev/news-aggregator.git
   ```

   and

   ```bash
   cd news-aggregator
   ```

2. **Set up Environment Variables**

   Duplicate the `.env.example` file to create a `.env` file:

   For Linux:

   ```bash
   cp .env.example .env
   ```

   For Windows:

   ```bash
   copy .env.example .env
   ```

## Setup Instructions without docker

1. **Set up Environment Variables**

   Update the `.env` file:

   ```env
   APP_NAME="News Aggregator API"
   APP_ENV=local
   APP_KEY=base64:your_generated_app_key
   APP_DEBUG=true
   APP_URL=http://localhost

   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=news-aggregator
   DB_USERNAME=root
   DB_PASSWORD=          # Leave empty if running without a password

   CACHE_DRIVER=redis
   QUEUE_CONNECTION=sync
   ```

2. **Generate Application Key**
   Run the following command to generate a new Laravel application key:

   ```bash
   php artisan key:generate
   ```

3. **Run on local machine**
   Install composer dependencies

   ```bash
   composer install
   ```

4. **Run Database Migrations and Seeders**

   After the containers are running, execute migrations with:

   ```bash
   php artisan migrate
   ```

   And seed the database with:

   ```bash
   php artisan db:seed
   ```

5. **Run on local machine**
   Run the following command and the application should be running on `http://localhost:8000`.

   ```bash
   php artisan serve
   ```

6. **Run Tests**
   Unit tests are available for key API endpoints. Run tests using:

   For Linux:

   ```bash
   touch database/database.sqlite
   ```

   For Windows:

   ```bash
   echo. > database/database.sqlite
   ```

   ```bash
   php artisan test
   ```

## Setup Instructions with docker

1. **Build and Start Docker Containers**

   Run the following command to build and start the Docker environment:

   ```bash
   docker compose -f docker/docker-compose.yml --env-file ./.env up --build
   ```

2. **Generate Application Key**

   After the containers are running, execute migrations with:

   ```bash
   docker exec -t laravelapp php artisan key:generate
   ```

3. **Run Database Migrations and Seeders**

   After the containers are running, execute migrations with:

   ```bash
   docker exec -t laravelapp php artisan migrate
   ```

   And seed the database with:

   ```bash
   docker exec -t laravelapp php artisan db:seed
   ```

4. **Run Tests**

   Run tests using:

   ```bash
   docker exec -t laravelapp php artisan test
   ```

## Running the Docker Environment

After completing the setup steps, the application should be running on http://localhost:8000.

To shut down the Docker environment, use:

```bash
docker compose -f docker/docker-compose.yml --env-file ./.env down
```

To rebuild the containers (if you make changes to Docker or environment configuration), use:

```bash
docker compose -f docker/docker-compose.yml --env-file ./.env up --build
```

## API Documentation

Generate API documentation using swagger.

Without docker:

```bash
php artisan l5-swagger:generate
```

With docker:

```bash
docker exec -t laravelapp php artisan l5-swagger:generate
```

The API documentation is generated using Swagger and is accessible at:

- [API Documentation (Swagger)](http://localhost:8000/api/documentation)
