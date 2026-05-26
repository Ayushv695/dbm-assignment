# Task DBM — Laravel 9 REST API

A full-featured REST API built with Laravel 9 covering authentication, project management, task management, queue jobs, and real-time notifications.

## Requirements

- PHP 8.1+
- Composer
- MySQL 8.0+
- File System (for caching and queues)
- Node.js (optional, for assets)

## Setup Steps

### 1. Clone & Install

```bash
git clone https://github.com/Ayushv695/dbm-assignment.git dbm-assignment
cd dbm-assignment
composer install
```

### 2. Configure Environment

Edit `.env` and set:

```
DB_DATABASE=dbm-assignment
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@taskdbm.com
```

### 3. Database Setup

Create the database, then run:

```bash
php artisan migrate
```

### 4. visit the base url

```bash
http://localhost/dbm-assignment/
```

### 6. Start Queue Worker

```bash
php artisan queue:work --tries=3
```

---

## API Endpoints
Base Url - http://localhost/dbm-assignment

### Authentication

| Method | Endpoint         | Auth Required | Description     |
|--------|------------------|---------------|-----------------|
| POST   | /api/register    | No            | Register user   |
| POST   | /api/login       | No            | Login           |
| POST   | /api/logout      | Yes           | Logout          |
| GET    | /api/profile     | Yes           | Get profile     |

### Projects

| Method | Endpoint              | Roles           | Description      |
|--------|-----------------------|-----------------|------------------|
| GET    | /api/projects         | All             | List projects    |
| POST   | /api/projects/store   | Admin, Manager  | Create project   |
| GET    | /api/projects/view/{id}    | All             | Project details  |
| PUT    | /api/projects/update/{id}    | Admin, Manager  | Update project   |
| DELETE | /api/projects/delete/{id}    | Admin, Manager  | Delete project   |

Query params: `?search=name&sort_by=created_at&sort_order=desc&per_page=15`

### Tasks

| Method | Endpoint           | Roles              | Description    |
|--------|--------------------|--------------------|----------------|
| GET    | /api/tasks         | All                | List tasks     |
| POST   | /api/tasks/store         | Admin, Manager     | Create task    |
| GET    | /api/tasks/view/{id}    | All                | Task details   |
| PUT    | /api/tasks/update/{id}    | Admin/Manager/Own  | Update task    |
| DELETE | /api/tasks/delete/{id}    | Admin, Manager     | Soft delete    |

Query params: `?status=pending&priority=high&assigned_to=2&search=title&sort_by=due_date`

### Dashboard

| Method | Endpoint                    | Roles           | Description       |
|--------|-----------------------------|-----------------|-------------------|
| GET    | /api/dashboard/analytics    | Admin, Manager  | Analytics data    |

---

## Role-Based Access

| Action                        | Admin | Manager | Employee |
|-------------------------------|-------|---------|----------|
| Register with any role        | ✓     | —       | —        |
| Create/Update/Delete Projects | ✓     | ✓       | ✗        |
| Create/Update/Delete Tasks    | ✓     | ✓       | ✗        |
| Update own task status        | ✓     | ✓       | ✓        |
| View all tasks                | ✓     | ✓       | ✗        |
| View own assigned tasks       | ✓     | ✓       | ✓        |
| View dashboard analytics      | ✓     | ✓       | ✗        |

---

## Architecture

```
app/
├── Http/
│   ├── Controllers/Api/     # Thin controllers — delegate to services
│   ├── Middleware/          # RoleMiddleware for RBAC
│   └── Requests/            # Form validation per endpoint
├── Models/                  # Eloquent models with relationships & scopes
├── Services/                # Business logic (AuthService, ProjectService, TaskService, DashboardService)
├── Jobs/                    # SendTaskAssignmentNotification (queued)
├── Events/                  # TaskAssigned (broadcastable)
└── Notifications/           # TaskAssignedNotification (email + DB)
```

---

## See Also

- Task-6,7,9 — answers to Tasks 6–9 (DB optimization, SQL queries, production scenarios)
