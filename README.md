# WorkFlow

WorkFlow is a multi-tenant employee workflow management system built with Laravel 12. It helps organizations manage day-to-day internal operations from a single web application, with each tenant isolated by its own database.

The current product includes tenant-specific login, team management, task tracking, leave management, document sharing, and activity logging.

## Highlights

- Multi-tenant architecture with dynamic database switching by tenant slug
- Role-based access for `admin`, `manager`, and `employee`
- Team creation and member assignment
- Task creation, assignment, editing, and status updates
- Leave application, approval, rejection, and balance tracking
- Document upload, preview, download, and visibility controls
- Tenant-aware session authentication
- Login tracking and tenant activity logs
- Public landing page for the product

## Tech Stack

- PHP 8.2+
- Laravel 12
- MySQL / MariaDB
- Blade templates
- Vite
- Tailwind CSS
- Alpine.js

## How It Works

WorkFlow uses a central database plus one database per tenant:

- The central database stores tenant metadata such as tenant name, slug, and database connection details.
- Each tenant database stores tenant users, teams, tasks, leaves, and activity data.
- Requests under `/org/{tenant}/...` pass through tenant middleware, which looks up the tenant by slug and reconnects Laravel to that tenant's database at runtime.

Example tenant login URL:

```text
/org/primenest/login
```

## Main Modules

### Tenant Authentication

- Session-based login using a dedicated `tenant` guard
- Remember me support
- Last login timestamp and IP tracking

### Teams

- Admin-only team management
- Assign a manager to a team
- Assign employees to teams

### Tasks

- Admin and managers can create and manage tasks
- Employees can view their assigned tasks
- Managers can update progress for the teams they oversee

### Leave Management

- Employees and managers can apply for leave
- Managers and admins can approve or reject leave requests
- Leave balances are tracked by leave type

### Documents

- Upload private, team, or organization-level documents
- Download and inline preview support for images and PDFs
- Visibility rules based on tenant role

### Activity Logging

- Logs major actions like task creation, completion, leave approval, and document upload

## Project Structure

Key folders in this repository:

- [app/Http/Controllers](C:/laragon/www/workflow/app/Http/Controllers) - tenant auth and module controllers
- [app/Http/Middleware](C:/laragon/www/workflow/app/Http/Middleware) - tenant database switching and role checks
- [app/Models](C:/laragon/www/workflow/app/Models) - central and tenant domain models
- [app/Console/Commands](C:/laragon/www/workflow/app/Console/Commands) - custom tenant migration command
- [database/migrations](C:/laragon/www/workflow/database/migrations) - central database migrations
- [database/migrations/tenant](C:/laragon/www/workflow/database/migrations/tenant) - tenant database migrations
- [resources/views](C:/laragon/www/workflow/resources/views) - Blade views for public and tenant interfaces
- [routes/web.php](C:/laragon/www/workflow/routes/web.php) - application routes

## Local Setup

### Prerequisites

Make sure you have the following installed:

- PHP 8.2 or newer
- Composer
- Node.js 18+ and npm
- MySQL or MariaDB
- Git
- A local PHP server stack such as Laragon, XAMPP, or Laravel Herd

### 1. Clone the repository

```bash
git clone <your-repository-url>
cd workflow
```

### 2. Install backend dependencies

```bash
composer install
```

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Create the environment file

```bash
cp .env.example .env
```

On Windows PowerShell, use:

```powershell
Copy-Item .env.example .env
```

### 5. Configure the central database

Update your `.env` file with the database credentials for the central application database:

```env
APP_NAME=WorkFlow
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=workflow
DB_USERNAME=root
DB_PASSWORD=
```

Create the `workflow` database in MySQL before running migrations.

### 6. Generate the application key

```bash
php artisan key:generate
```

### 7. Run central migrations

```bash
php artisan migrate
```

This creates the central tables, including the `tenants` table.

### 8. Create a tenant database

Create a separate database for your tenant. Example:

```sql
CREATE DATABASE workflow_primenest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 9. Register the tenant in the central database

You can create the tenant record using Tinker:

```bash
php artisan tinker
```

Then run:

```php
\App\Models\Tenant::create([
    'name' => 'PrimeNest',
    'slug' => 'primenest',
    'db_name' => 'workflow_primenest',
    'db_username' => 'root',
    'db_password' => '',
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'status' => 'active',
]);
```

### 10. Run tenant migrations

```bash
php artisan tenants:migrate
```

This command loops through all registered tenants and applies the migrations inside [database/migrations/tenant](C:/laragon/www/workflow/database/migrations/tenant).

### 11. Create a tenant admin user

Open Tinker again:

```bash
php artisan tinker
```

Then create an admin in the tenant database:

```php
config([
    'database.connections.tenant.database' => 'workflow_primenest',
    'database.connections.tenant.username' => 'root',
    'database.connections.tenant.password' => '',
    'database.connections.tenant.host' => '127.0.0.1',
    'database.connections.tenant.port' => '3306',
]);

DB::purge('tenant');
DB::reconnect('tenant');

\App\Models\TenantUser::create([
    'name' => 'Tenant Admin',
    'email' => 'admin@primenest.test',
    'password' => bcrypt('password'),
    'role' => 'admin',
]);
```

### 12. Create the storage symlink

```bash
php artisan storage:link
```

### 13. Build frontend assets

For development:

```bash
npm run dev
```

For a production build:

```bash
npm run build
```

### 14. Start the application

You can run Laravel locally with:

```bash
php artisan serve
```

Then open:

```text
http://127.0.0.1:8000
```

Tenant login example:

```text
http://127.0.0.1:8000/org/primenest/login
```

If you use Laragon or Apache, point your local virtual host to the repository's `public` directory.

## Helpful Commands

```bash
php artisan migrate
php artisan tenants:migrate
php artisan serve
php artisan test
npm run dev
npm run build
```

## Testing

Run the test suite with:

```bash
php artisan test
```

At the moment, the repository appears to contain only the default example tests, so expanding automated coverage would be a good next step.

## Deployment Notes

- Ensure the web root points to the `public` directory.
- Keep [public/.htaccess](C:/laragon/www/workflow/public/.htaccess) in place for Apache-based routing.
- Build assets with `npm run build` before deployment if your server does not build them during CI/CD.
- Run both central migrations and tenant migrations during deployment.
- If using HTTPS behind a proxy, the app already includes scheme forcing for forwarded HTTPS requests in production.

## Known Setup Notes

- This repository does not currently include a full tenant seeder for demo data.
- Tenant provisioning is partly manual unless you add an admin UI or command for creating tenant records and initial users.
- The central `DatabaseSeeder` is still the default Laravel example seeder and is not enough for a complete product demo.

## Roadmap Ideas

Based on the current UI and codebase direction, the project is moving toward:

- schedule and calendar management
- richer user profiles
- personal activity insights
- EOD reporting

## Contributing

If you plan to contribute:

1. Fork the repository.
2. Create a feature branch.
3. Make your changes.
4. Run tests and build assets.
5. Open a pull request with a clear summary.

## License

This project is currently distributed under the [MIT License](https://opensource.org/licenses/MIT), following the Laravel project base, unless you decide to replace it with a project-specific license.
