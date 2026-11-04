# Project Architecture: SiSehat

## Overview
SiSehat is a Laravel-based web application designed to assess the health and performance of UMKM (Micro, Small, and Medium Enterprises). It provides a platform for business owners to evaluate their operations across various factors and receive recommendations.

## Tech Stack
- **Framework**: [Laravel 13](https://laravel.com/)
- **Frontend**: [Blade Templates](https://laravel.com/docs/blade), [Tailwind CSS](https://tailwindcss.com/)
- **Build Tool**: [Vite](https://vitejs.dev/)
- **Database**: MySQL/MariaDB (configured via `.env`)
- **Authentication**: [Laravel Breeze](https://laravel.com/docs/breeze)
- **PDF Generation**: [Laravel DomPDF](https://github.com/barryvdh/laravel-dompdf)

## System Architecture

### Core Components
The application follows the Model-View-Controller (MVC) pattern provided by Laravel.

#### 1. Models (`app/Models`)
- **`User`**: Handles authentication and user roles (Owners, Employees).
- **`Umkm`**: Represents the business entity being assessed.
- **`Assessment`**: Stores evaluation data, scores, and status.
- **`Factor`**: Defines the criteria or dimensions used in assessments.

#### 2. Controllers (`app/Http/Controllers`)
- **`DashboardController`**: The primary controller handling the main dashboard, UMKM management, and assessment flows.
- **`ProfileController`**: Manages user profile updates and account deletion.

#### 3. Middleware (`app/Http/Middleware`)
- **`NonEmployeeMiddleware`**: Restricts certain management features to UMKM owners only.
- **`Auth` / `Verified`**: Standard Laravel authentication and email verification.

#### 4. Views (`resources/views`)
- Uses Blade components for modular UI elements.
- Layouts are managed via `AppLayout` and `GuestLayout` components.

### Key Modules

#### Assessment System
- Users can perform organizational assessments.
- Scores are calculated based on multiple factors (`score_ins`, `score_ldi`, etc.).
- Summaries can be exported as PDF reports.

#### UMKM & Employee Management
- Owners can register their UMKM.
- Owners can add and manage employees who can participate in assessments.

#### Data Visualization
- The dashboard provides a visual overview of assessment results and recommendations.

## Database Schema Highlights
- **`users`**: `id`, `name`, `email`, `role`, etc.
- **`umkm`**: `id_umkm`, `nama_umkm`, `industry`, `id_user`.
- **`assessments`**: `id_assessment`, `id_umkm`, `total_score`, `answers` (JSON), `status`.
- **`factors`**: `id_factor`, `name`, `weight`.

## Development Workflow
- **Styles**: Tailwind CSS processed via Vite.
- **Routes**:
    - `web.php`: Primary web interface routes.
    - `auth.php`: Authentication-related routes (Login, Register, Password Reset).
- **Scripts**: Several utility scripts exist in the root (e.g., `check_score.php`) for maintenance and debugging.

## Directory Structure
- `app/`: Core application logic (Models, Controllers, Middleware).
- `config/`: Application configuration files.
- `database/`: Migrations and seeders.
- `public/`: Entry point and compiled assets.
- `resources/`: Raw assets (JS/CSS) and Blade views.
- `routes/`: Routing definitions.
- `storage/`: Logs, compiled views, and file uploads.
- `tests/`: Feature and Unit tests using Pest.
