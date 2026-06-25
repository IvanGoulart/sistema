# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

SaaS scheduling system (sistema de agendamento) for beauty/service businesses, branded as "Salão Fácil". Built on Laravel 10 + Livewire 3 + MySQL 8.4 with the Materio Bootstrap 5 admin template. Two separate panels with different layouts and auth flows:

- **Admin panel** (`/dashboard`, `/admin`) — manage schedule, services, users, availability
- **Client portal** (`/portal`) — clients book and view appointments

## Commands

```bash
# Start development (run all three concurrently)
./vendor/bin/sail up -d      # Docker containers (MySQL, etc.)
php artisan serve            # Laravel dev server
npm run watch                # Compile assets with hot reload

# Build assets
npm run dev       # development build
npm run prod      # production build

# Database
php artisan migrate
php artisan db:seed                              # dev data (PermissionsTableSeeder etc.)
php artisan db:seed --class=ProductionSeeder     # prod: creates admin@salaofacil.digital

# Run tests
php artisan test
php artisan test tests/Feature/ExampleTest.php   # single test file

# Useful during development
php artisan route:list --path=portal -v
php artisan route:clear && php artisan config:clear && php artisan view:clear
php artisan tinker
./vendor/bin/pint                                # PHP code style fixer
```

## Architecture

### Multi-Tenant Isolation

Every query against business data must be filtered by `tenant_id`. The current tenant is stored in the session:

```php
$tenantId = session('tenant_id') ?? 1;
DB::table('services')->where('tenant_id', $tenantId)->get();
```

Tables with `tenant_id`: `services`, `schedules`, `employee_weekly_schedules`, `user_permissions`.

### Permission System

There are two distinct layers — do not conflate them:

**1. Per-tenant roles** — `admin`, `employee`, `client`. Stored in `user_permissions` (with `tenant_id`), checked via middleware alias `permission`. These are scoped to the active tenant (`session('tenant_id')`):

```php
Route::middleware(['auth', 'permission:admin'])->group(...)
Route::middleware(['auth', 'permission:client'])->group(...)
```

`User::hasPermission(string $name, ?int $tenantId = null)` queries `user_permissions` joined to `permissions`, filtered by the given (or active) tenant. The correct way to read a user's role in views:

```php
$user->permissions->first()?->name   // ✓ correct
$user->userPermission->permission->name  // ✗ relation does not exist
```

Note that `admin` here means "owner of a salon (tenant)" — it is NOT the platform owner. Many salon admins coexist, each scoped to their own tenant.

**2. Platform super-admin** — the SaaS owner (you). This is a GLOBAL flag, `users.is_super_admin` (boolean), independent of any tenant. It is NOT in `user_permissions`. Check it via `User::isSuperAdmin()` and guard routes with the `platform` middleware alias (`App\Http\Middleware\PlatformAdmin`):

```php
Route::middleware(['auth', 'platform'])->group(...)  // only the platform owner
```

Tenant management (`/tenant/*` → `App\Livewire\Tenant\FormCreateTenant`) lives behind `platform`. **A salon admin must never reach tenant CRUD** — that would let them see/edit/delete other companies. Because Livewire actions hit `/livewire/update` (outside route middleware), `FormCreateTenant` re-checks authorization in `mount()` and in every mutating action (`save`/`delete`/`openEdit`/`confirmDelete`) via `abort_unless($user->isSuperAdmin(), 403)`.

Mark super-admins via seeders (`ProductionSeeder`, `UsersTableSeeder`). After deploying the `is_super_admin` migration to an existing DB, run `ProductionSeeder` (or set the flag manually) — existing users default to `false`.

Menu items can be hidden from non-super-admins with `"platform": true` in `resources/menu/verticalMenu.json` (filtered in `submenu.blade.php`).

### Repository Pattern

Business logic is behind interfaces bound in `RepositoryServiceProvider`:
- `UserRepositoryInterface` → `UserRepository`
- `PermissionRepositoryInterface` → `PermissionRepository`
- `ScheduleRepositoryInterface` → `ScheduleRepository`

Inject interfaces in controllers, not concrete classes.

### Livewire Components

All interactive UI uses Livewire 3. Key components:

| Component tag | Class | Purpose |
|---|---|---|
| `livewire:services.service-manager` | `App\Livewire\Services\ServiceManager` | Service CRUD + employee linking |
| `livewire:schedule.admin-agenda` | `App\Livewire\Schedule\AdminAgenda` | Weekly agenda grid with filters |
| `livewire:schedule.employee-availability` | `App\Livewire\Schedule\EmployeeAvailability` | Weekly availability toggle per employee |
| `livewire:form-create-agenda` | `App\Livewire\FormCreateAgenda` | Booking form (used in both admin and portal) |
| `livewire:tenant.form-create-tenant` | `App\Livewire\Tenant\FormCreateTenant` | Tenant CRUD inline |
| `livewire:reports.agenda-report` | `App\Livewire\Reports\AgendaReport` | Filterable appointment report |

### View Layouts

- **Admin**: `layouts/contentNavbarLayout.blade.php` — uses Materio template classes
- **Portal**: `layouts/portal.blade.php` — plain Bootstrap 5 via CDN (no Materio classes)
- **Blank**: `layouts/blankLayout.blade.php` — for auth pages

The nav menu is driven by `resources/menu/verticalMenu.json`, shared to all views via `MenuServiceProvider`.

### Asset Pipeline

Laravel Mix (webpack.mix.js) compiles assets from `resources/assets/` to `public/assets/`. The structure mirrors vendor (Bootstrap, MDI icons, Perfect Scrollbar) and app-level JS/CSS. After any change to `resources/assets/`, run `npm run dev`.

## Key Conventions

### Destructive Action Confirmation

Never use `confirm()` in the browser. Always use inline Livewire confirmation:

```php
public ?int $confirmingDeleteId = null;
public function confirmDelete(int $id): void { $this->confirmingDeleteId = $id; }
public function dismissDelete(): void { $this->confirmingDeleteId = null; }
public function delete(int $id): void {
    // perform delete
    $this->confirmingDeleteId = null;
}
```

### Badge Colors: Admin vs Portal

The portal layout loads Bootstrap 5 via CDN without Materio — `bg-label-*` classes do not exist there. Use inline `rgba()` in portal views:

```html
<!-- Portal views: rgba inline -->
<span style="background:rgba(255,171,0,.15);color:#a07800;">Label</span>

<!-- Admin views: Materio classes work fine -->
<span class="badge bg-label-warning">Label</span>
```

### Auth Routing

- Admin login: `GET /admin` → `POST /auth/check-authenticate`
- Portal login: `GET /portal/login` → `POST /portal/login`
- `Authenticate` middleware redirects unauthenticated `/portal/*` requests to `portal.login`
- Clients cannot access `/dashboard` (403); admins cannot access `/portal/agendar` (403)

### Landing Page / Lead Capture

`GET /` → `LandingController@index` renders `landing.blade.php`. Lead form at `POST /interesse` (throttled: 3 requests per 10 min per IP) saves to `leads` table and sends `NewLeadMail` to `config('mail.from.address')`. Mail failure is caught and logged — it does not break the form.

## Database Schema Reference

```
users: id, name, email, password, active, is_super_admin (platform owner, global)
permissions: id, name (admin|employee|client)  -- per-tenant roles, NOT super-admin
user_permissions: id, tenant_id, user_id, code_permission (FK→permissions.id)
tenants: id, name, slug, email, phone, is_active
tenant_user: tenant_id, user_id, role, is_active
services: id, tenant_id, name, description
user_services: user_id, service_id  (employees ↔ services)
schedules: id, tenant_id, employee_id, client_id, service_id, day (date), hour (time), cancel (bool)
employee_weekly_schedules: id, tenant_id, employee_id, day_of_week (0=Sun…6=Sat), start_time, end_time
  UNIQUE(tenant_id, employee_id, day_of_week)
leads: id, name, whatsapp, business_type, created_at
```

## Development Credentials (local seeder)

| Role | URL | Email | Password |
|---|---|---|---|
| Admin (also super-admin) | `/admin` | `admin@sistema.test` | `password` |
| Employee (professional) | `/admin` | `profissional@sistema.test` | `password` |
| Client | `/portal/login` | `user1@sistema.test` | `password` |

`admin@sistema.test` is seeded with `is_super_admin = true`, so it can reach tenant management (`/tenant`). The other roles get 403 there.

Production admin (via `ProductionSeeder`): `admin@salaofacil.digital` / `admin@2026` (also super-admin)
