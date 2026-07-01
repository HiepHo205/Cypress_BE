Cypress BE

A modern backend application built with Laravel 11/12, PHP 8.3+, and GraphQL (via Lighthouse)[cite: 1]. The project follows a Feature-Based Architecture (Domain/Module Layer) combined with Technical Layering, organizing business logic into independent modules while keeping core resources centralized. This structure improves scalability, maintainability, and code organization for large-scale applications.

Getting Started

Install project dependencies:
composer install

Run the development server:
php artisan serve

Open your browser at GraphiQL Playground:
http://localhost:8000/graphiql

The application exposes a single GraphQL endpoint at `http://localhost:8000/graphql` to handle all incoming frontend requests.

Project Structure

CYPRESS_BE/
│
├── app/
│   ├── GraphQL/         # GraphQL Handlers (Queries & Mutations)
│   ├── Models/          # Eloquent Models (User, Company, ...)[cite: 1]
│   ├── Services/        # Core Business Logic Services
│   ├── Providers/       # Application Service Providers[cite: 1]
│   └── Http/            # Standard HTTP Layer (Controllers, Middleware)[cite: 1]
│
├── graphql/             # GraphQL Schema Definitions
│   ├── modules/         # Schema definitions split by business features
│   └── schema.graphql   # Main entry point schema file
│
├── config/              # Application Configurations
├── database/            # Migrations, Factories, and Seeders[cite: 1]
├── routes/              # Application Routes[cite: 1]
├── storage/             # File Storage & Logs[cite: 1]
├── tests/               # Automated Tests (Unit & Feature)[cite: 1]
│
├── artisan[cite: 1]
├── composer.json[cite: 1]
└── README.md[cite: 1]

Architecture

The project adopts a Feature-Based Architecture for GraphQL components, where each business domain is split into independent schema files and structured backend handlers.

> ⚠️ **TEAM WORK NOTE:**
> To maintain the empty folder structure on Git, placeholder `.md` files are placed inside modules. **When you start coding a feature, please change the file extension to `.php`** (e.g., `Login.md` -> `Login.php`) or create a new `.php` file in that directory.

Example:
graphql/modules/
├── auth.graphql
├── user.graphql
└── company.graphql

app/GraphQL/
│
├── Queries/             # For Fetching Data (Equivalent to GET)
│   ├── User/
│   │   └── UserQuery.php
│   └── Company/
│
└── Mutations/           # For Modifying Data (Equivalent to POST/PUT/DELETE)
    ├── Auth/
    │   ├── Login.php
    │   └── Logout.php
    └── User/
        └── CreateUser.php

app/Services/            # Core business logic isolated from GraphQL layer
├── UserService.php
└── CompanyService.php

Technologies

Laravel 11/12 Framework[cite: 1]
PHP 8.3+[cite: 1]
GraphQL (via Nuwave/Lighthouse)
GraphiQL (Development IDE Tools)
Eloquent ORM[cite: 1]
PHPUnit (Testing)[cite: 1]

Scripts

composer install     # Install PHP packages
php artisan serve    # Start development backend server
php artisan migrate  # Run database migrations[cite: 1]
php artisan db:seed  # Seed database with sample data
php artisan test     # Run automated test suites[cite: 1]

Deployment

The application can be deployed to:
Laravel Forge
Docker (Supports Docker Compose setup)[cite: 1]
AWS (Elastic Beanstalk / EC2)[cite: 1]
DigitalOcean
Heroku

License

This project is intended for learning and internal development purposes.