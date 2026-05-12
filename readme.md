# SuperVMar — Product & Sales Management System

Full-stack web application for supermarket management: products, sales, stock, in-store product location and worker notifications.

---

## Description

SuperVMar is a full-stack web application for the management of a supermarket.

The system covers:

- **Product management**: catalogue, stock, pricing, taxes, suppliers and categories
- **Sales management**: sale lines, order completion, history and reports
- **In-store product location**: interactive visual map of supermarket zones with product-to-position assignment
- **User and worker management**: roles, permissions and task assignment per zone
- **Push notifications**: automatic alerts to restocking workers when a product's stock runs low
- **Secure authentication**: JWT with token refresh

---

## Architecture

The project is split into two decoupled applications that communicate through a REST API:

```
┌─────────────────────┐        HTTP / JSON         ┌──────────────────────┐
│   Frontend (SPA)    │ ◄────────────────────────► │    Backend (API)     │
│   React + Vite      │                            │    Symfony 7.2       │
└─────────────────────┘                            └──────────────────────┘
                                                            │
                                                   ┌────────┴────────┐
                                                   │   MySQL 8.0     │
                                                   └────────┬────────┘
                                                            │
                                                   ┌────────┴────────┐
                                                   │   RabbitMQ 4    │
                                                   └─────────────────┘
```

### Patterns and principles

- **Hexagonal Architecture** (Ports & Adapters)
- **Domain-Driven Design (DDD)** — 12 bounded contexts
- **CQRS** — separation of Commands and Queries
- **Event-Driven** — domain events processed asynchronously via RabbitMQ

### Bounded Contexts

| Context | Responsibility |
|---|---|
| `Product` | Catalogue, stock, images |
| `Sale` | Sales and sale lines |
| `User` | System users |
| `Supermarket` | Supermarkets, halls and zones |
| `ProductAllocation` | In-store product location |
| `AllocateWorker` | Worker assignment to zones |
| `Category` | Product categories |
| `Tax` | VAT types |
| `Supplier` | Suppliers |
| `Job` | Job positions |
| `Notification` | Push subscriptions and notifications |
| `Authentication` | JWT authentication |

---

## Tech Stack

### Backend — `api/`

| Technology | Version | Purpose |
|---|---|---|
| PHP | 8.4+ | Main language |
| Symfony | 7.2 | Web framework |
| Doctrine DBAL / ORM | 3.x / 3.x | Database access |
| MySQL | 8.0 | Relational database |
| RabbitMQ | 4 | Async message queue |
| Symfony Messenger | 7.2 | RabbitMQ integration |
| LexikJWT | 3.2 | JSON Web Token authentication |
| Minishlink Web-Push | 10.0 | Push notifications (VAPID) |
| Ramsey UUID | 4.7 | UUID generation |
| PHPUnit | 13.1 | Testing |

### Frontend — `frontend/`

| Technology | Version | Purpose |
|---|---|---|
| React | 19.1 | UI library |
| TypeScript | 5.9 | Static typing |
| Vite | 7.1 | Bundler and dev server |
| Material UI (MUI) | 7.3 | UI components |
| MUI X Charts / DataGrid | 8.x | Charts and data tables |
| Toolpad Core | 0.16 | Application shell |
| Konva / React-Konva | 10.x / 19.x | 2D canvas for the floor map |
| React Router | 7.9 | SPA routing |
| UUID | 13.0 | Client-side ID generation |

### Infrastructure

| Service | Docker image |
|---|---|
| API PHP-FPM | Custom (PHP 8.4 + Xdebug) |
| Web server | `nginx:stable-alpine` |
| Database | `mysql:8.0` |
| Message queue | `rabbitmq:4-management` |
| Supervisor | Consumer process management |

---

## Repository Structure

```
TFG2024/
├── api/                        # Symfony backend
│   ├── api-vmar/               # API source code
│   │   ├── src/                # Bounded contexts (DDD)
│   │   ├── tests/              # Test suite (Unit/Integration/Functional)
│   │   ├── config/             # Symfony configuration
│   │   └── migrations/         # Database migrations
│   ├── nginx/                  # Nginx configuration
│   ├── php/                    # PHP-FPM and Xdebug configuration
│   ├── supervisor/             # RabbitMQ consumer configuration
│   └── docker-compose.yml      # Container orchestration
└── frontend/                   # React frontend
    └── supervmar/
        ├── src/
        │   ├── modules/        # Domain modules
        │   │   ├── product/
        │   │   ├── sale/
        │   │   ├── user/
        │   │   ├── supermarket/
        │   │   ├── notification/
        │   │   └── commons/
        │   ├── App.tsx
        │   └── main.tsx
        └── vite.config.ts

```

---

## Getting Started

### Prerequisites

- [Docker](https://docs.docker.com/engine/install/) installed and running
- [Node.js](https://nodejs.org/) 20+ (for running the frontend locally)
- [Git](https://git-scm.com/)

### 1. Start the API

```bash
cd api/

# Copy the environment file and adjust variables if needed
cp .env.example .env          # or edit .env directly

# Build and start all services
docker compose up -d --build

# Run database migrations
docker exec superubemar_php bash -c "cd /var/www/html/api-vmar && php bin/console doctrine:migrations:migrate --no-interaction"
```

The API will be available at **http://localhost:8080**

The RabbitMQ management panel will be at **http://localhost:15672** (user/pass)

### 2. Start the RabbitMQ consumers

Consumers are managed automatically by Supervisor inside the PHP container.  
To start them manually:

```bash
docker exec superubemar_php bash -c "cd /var/www/html/api-vmar && php bin/console rabbitmq:consume SubtractQuantityOnProductScanned"
docker exec superubemar_php bash -c "cd /var/www/html/api-vmar && php bin/console rabbitmq:consume SubtractStockOnSaleFinished"
```

### 3. Start the frontend

```bash
cd frontend/supervmar/

# Install dependencies
npm install

# Development server
npm run dev
```

The frontend will be available at **http://localhost:5173**

---

## Tests

The API has a complete test suite.

```bash
# Run all tests
docker exec superubemar_php bash -c "cd /var/www/html/api-vmar && APP_ENV=test php bin/phpunit"

# Run only unit tests
docker exec superubemar_php bash -c "cd /var/www/html/api-vmar && APP_ENV=test php bin/phpunit --testsuite Unit"

# Run only integration tests
docker exec superubemar_php bash -c "cd /var/www/html/api-vmar && APP_ENV=test php bin/phpunit --testsuite Integration"

# Run only functional tests
docker exec superubemar_php bash -c "cd /var/www/html/api-vmar && APP_ENV=test php bin/phpunit --testsuite Functional"
```

### Coverage by type

| Suite | Tests | What it validates |
|---|---|---|
| **Unit** | ~226 | Domain logic and application handlers |
| **Integration** | ~105 | DBAL repositories against a real database |
| **Functional** | ~52 | Full HTTP endpoints (auth, CRUD, error handling) |

---

## Key Features

### Interactive floor map
2D canvas visualization (Konva) of the supermarket layout with zones and aisles. Allows assigning products to specific positions and viewing available stock per location.

### Push notifications
When a product's stock drops below the configured threshold, a domain event is published to RabbitMQ. The consumer sends a Web Push notification (VAPID) to all restocking workers assigned to that zone.

### JWT authentication
Login with email and password. The JWT token is stored on the client and sent in the `Authorization: Bearer` header on every request. Roles (`ROLE_ADMIN`, `ROLE_USER`) control access to endpoints.

---

## License

All rights reserved — Francisco Jesús García López, 2025-2026.

