# silex-jwt-rest-api

## Description
Simple REST API built with Silex micro-framework, JWT token for authentication, Phinx for database migrations and seeding, Doctrine DBAL for database operations and Symfony Validator component for incoming data validation.

## Installation
1. Configure .env file.
2. Run Phinx migration with command:
```
vendor/bin/phinx migrate
```
3. Run Phinx seeder with commands **(commands order is important)**:
```
vendor/bin/phinx seed:run -s UserSeeder
vendor/bin/phinx seed:run -s CategorySeeder
vendor/bin/phinx seed:run -s ProductSeeder
```