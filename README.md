# Lingid
ilmarakendus - https://hajusrakendus-main-sdvtix.laravel.cloud/weather  
kaardirakendus - https://hajusrakendus-main-sdvtix.laravel.cloud/map  
blogi - https://hajusrakendus-main-sdvtix.laravel.cloud/blog  


# Laravel Vue Starter Kit

This is a skeleton application for the Laravel framework using Vue.js with Inertia.js.

## Technologies Used

*   **Backend:** PHP 8.2+, [Laravel](https://laravel.com/) 12+
*   **Frontend:** [Vue.js](https://vuejs.org/) (via [Inertia.js](https://inertiajs.com/)), [Vite](https://vitejs.dev/), [Tailwind CSS](https://tailwindcss.com/)
*   **Database:** [PostgreSQL](https://www.postgresql.org/) (configured in [`config/database.php`](config/database.php) and [`docker-compose.yml`](docker-compose.yml))
*   **Caching/Queues:** [Redis](https://redis.io/) (configured in [`config/database.php`](config/database.php), [`config/cache.php`](config/cache.php), [`config/queue.php`](config/queue.php) and [`docker-compose.yml`](docker-compose.yml))
*   **Development Environment:** [Docker](https://www.docker.com/), [Laravel Sail](https://laravel.com/docs/sail)
*   **Package Managers:** [Composer](https://getcomposer.org/), [PNPM](https://pnpm.io/)
*   **Testing:** [Pest](https://pestphp.com/) and [`composer.json`](composer.json)
*   **Linting/Formatting:** [Laravel Pint](https://laravel.com/docs/pint) (configured in [`.github/workflows/lint.yml`](.github/workflows/lint.yml)), [ESLint](https://eslint.org/) (configured in [`eslint.config.js`](eslint.config.js)), [Prettier](https://prettier.io/) (configured in [`.prettierrc`](.prettierrc))

## Getting Started

### Prerequisites

*   [Docker Desktop](https://www.docker.com/products/docker-desktop/)
*   [Composer](https://getcomposer.org/download/)
*   [Node.js](https://nodejs.org/) (includes npm)
*   [PNPM](https://pnpm.io/installation)

### Installation & Setup

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd <repository-directory>
    ```

2.  **Copy the environment file:**
    Laravel utilizes a `.env` file for environment configuration. Copy the example file:
    ```bash
    cp .env.example .env
    ```

3.  **Install PHP Dependencies:**
    ```bash
    composer install
    ```

4.  **Install Node.js Dependencies:**
    Using PNPM (recommended):
    ```bash
    pnpm install
    ```
    Or using NPM:
    ```bash
    npm install
    ```

5.  **Start Docker Containers:**
    Use Laravel Sail to build and start the Docker containers (web server, database, Redis, etc.).
    ```bash
    ./vendor/bin/sail up -d
    ```

6.  **Generate Application Key:**
    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

7.  **Run Database Migrations:**
    ```bash
    ./vendor/bin/sail artisan migrate
    ```

8.  **Build Frontend Assets:**
    For development with hot module replacement (HMR):
    ```bash
    pnpm run dev
    # or
    npm run dev
    ```
    For production build:
    ```bash
    pnpm run build
    # or
    npm run build
    ```

9.  **Access the Application:**
    Open your web browser and navigate to the `APP_URL` specified in your `.env` file (defaults to `http://localhost` which maps to port 80 via [`docker-compose.yml`](docker-compose.yml)). The Vite development server runs on port 5173 by default.

## Development Workflow

*   **Running Artisan Commands:** Prefix commands with `./vendor/bin/sail`. Example: `./vendor/bin/sail artisan list`.
*   **Linting/Formatting:**
    ```bash
    # PHP Formatting
    ./vendor/bin/sail ./vendor/bin/pint

    # Frontend Formatting
    pnpm run format
    # or
    npm run format

    # Frontend Linting
    pnpm run lint
    # or
    npm run lint
    ```
*   **Stopping Docker Containers:**
    ```bash
    ./vendor/bin/sail down
    ```
