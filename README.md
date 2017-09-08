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

## Endpoints
``POST /api/v1/signup`` - signup description.
#### Parameters
Name|Description
-|-
username|User name.
password|User password.
#### Responses
Code|Description
-|-
201|Return when user account has been created correctly.
400|Return when parameters is not valid with information about validation error.
---
``POST /api/v1/signin`` - signin description.
#### Parameters
Name|Description
-|-
username|User name.
password|User password.
#### Responses
Code|Description
-|-
200|Return JWT token when user credentials are valid.
400|Return when request parameters are not complete.
401|Return when credentials are invalid.
---
``GET /api/v1/products`` - get products description.
#### Parameters
Name|Description
-|-
fields[]|List of fields which should be returned in response, comma seperated. Available fields: *id, category_id, user_id, name, description, price, created_at*.
includeUser|Return products with user relationship.
includeCategory|Return products with category relationship.
where, orWhere, andWhere|Filter products with where statement, for example: (where, orWhere, andWhere) = (fieldName), (eq, neq, lt, lte, gt, gte), (value).
orderBy[]|Order by products by fields, for example: orderBy[] = (fieldName), (ASC, DESC).
page|Set page of products.
itemsPerPage|Set items per page in pagination.
#### Responses
Code|Description
-|-
200|Return JWT token when user credentials is valid.
400|Return when request parameters are not complete.
401|Return when request is invalid or user is unauthorized.