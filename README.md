# Cypress BE

A modern backend application built with Laravel 13, PHP 8.3+, and GraphQL (via Lighthouse). The project follows a Feature-Based Architecture (Domain/Module Layer) combined with Technical Layering, organizing business logic into independent modules while keeping core resources centralized. This structure improves scalability, maintainability, and code organization for large-scale applications.

---

## Getting Started

Install project dependencies (PHP & Node packages):
```bash
composer install
npm install
Run the development server:

Bash
php artisan serve
Open your browser at GraphiQL Playground:

Plaintext
http://localhost:8000/graphiql
The application exposes a single GraphQL endpoint at http://localhost:8000/graphql to handle all incoming frontend requests.

Project Structure
Plaintext
CYPRESS_BE/
│
├── app/
│   ├── GraphQL/         # GraphQL Handlers (Queries & Mutations)
│   ├── Models/          # Eloquent Models (User, Company, ...)
│   ├── Services/        # Core Business Logic Services
│   ├── Providers/       # Application Service Providers
│   └── Http/            # Standard HTTP Layer (Controllers, Middleware)
│
├── graphql/             # GraphQL Schema Definitions
│   ├── modules/         # Schema definitions split by business features
│   └── schema.graphql   # Main entry point schema file
│
├── config/              # Application Configurations
├── database/            # Migrations, Factories, and Seeders
├── routes/              # Application Routes
├── storage/             # File Storage & Logs[cite: 1]
├── tests/               # Automated Tests (Unit & Feature)[cite: 1]
│
├── .husky/              # Git Hooks configuration (Pre-commit formatting)
├── artisan[cite: 1]
├── composer.json[cite: 1]
└── README.md[cite: 1]
Architecture
The project adopts a Feature-Based Architecture for GraphQL components, where each business domain is split into independent schema files and structured backend handlers.

⚠️ TEAM WORK NOTE:
To maintain the empty folder structure on Git, placeholder .md files are placed inside modules. When you start coding a feature, please change the file extension to .php (e.g., Login.md -> Login.php) or create a new .php file in that directory.

Example:
graphql/modules/

Plaintext
├── auth.graphql
├── user.graphql
└── company.graphql
app/GraphQL/

Plaintext
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
app/Services/ (Core business logic isolated from GraphQL layer)

Plaintext
├── UserService.php
└── CompanyService.php
Technologies
Laravel 13 Framework

[cite: 1]

PHP 8.3+

[cite: 1]

GraphQL (via Nuwave/Lighthouse)

GraphiQL (Development IDE Tools)

Eloquent ORM

[cite: 1]

Redis (Predis Client for Cache & Queue)

Prettier (with PHP Plugin for Code Formatting)

Husky (Git Hooks for Pre-commit Linting)

PHPUnit (Testing)[cite: 1]

Scripts
Bash
composer install                     # Install PHP packages
npm install                          # Install Node packages & activate Husky hooks
php artisan serve                    # Start development backend server
php artisan migrate                  # Run database migrations[cite: 1]
php artisan db:seed                  # Seed database with sample data
php artisan test                     # Run automated test suites[cite: 1]
npx prettier --write "app/**/*.php"  # Manually format all PHP files
Deployment
The application can be deployed to:

Laravel Forge

Docker (Supports Docker Compose setup)[cite: 1]

AWS (Elastic Beanstalk / EC2)[cite: 1]

DigitalOcean

Heroku

License
This project is intended for learning and internal development purposes.