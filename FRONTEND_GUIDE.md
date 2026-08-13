# 🚀 InternTrack — Comprehensive Frontend Developer Guide & File Customization Reference

Welcome to **InternTrack**! This guide is written specifically for frontend engineers. Whether you are **customizing the existing Blade UI** or **building a brand new frontend** (React, Next.js, Vue, React Native, Flutter), this guide tells you **exactly where every file lives, what each file does, how to modify each frontend file, how to set up the environment, and how to use the REST APIs**.

---

## 📑 Table of Contents
1. [Quick Environment Setup (Step-by-Step)](#1-quick-environment-setup-step-by-step)
2. [Complete Directory & File Reference (Konsi File Me Kya Hai)](#2-complete-directory--file-reference-konsi-file-me-kya-hai)
3. [How to Modify Every Frontend File (Kis File Ko Kaise Change Kare)](#3-how-to-modify-every-frontend-file-kis-file-ko-kaise-change-kare)
   - [🛠️ Modifying Layouts](#-modifying-layouts-resourcesviewslayouts)
   - [🧩 Modifying Components](#-modifying-components-resourcesviewscomponents)
   - [📄 Modifying Page Views](#-modifying-page-views-resourcesviews)
   - [⚡ Building a Decoupled React / Next.js SPA](#-building-a-decoupled-react--nextjs-spa)
4. [Authentication Architecture (Web Session vs API Token)](#4-authentication-architecture-web-session-vs-api-token)
5. [Role-Based Access & Landing Routes](#5-role-based-access--landing-routes)
6. [Complete REST API Specification (`/api/v1/*`)](#6-complete-rest-api-specification-apiv1)
7. [Frontend Business Rules & Logic Constraints](#7-frontend-business-rules--logic-constraints)
8. [Seeded Test Accounts & Passwords](#8-seeded-test-accounts--passwords)
9. [Troubleshooting & Common Commands](#9-troubleshooting--common-commands)

---

## 1. Quick Environment Setup (Step-by-Step)

Follow these exact steps to get InternTrack running on your machine:

### Step 1: PHP & Extension Setup
Ensure you have **PHP 8.2+** installed (e.g., via XAMPP at `C:\xampp\php\php.exe`).
Make sure the following extensions are enabled in your `php.ini`:
- `extension=pdo_pgsql`
- `extension=pgsql`
- `extension=zip`
- `extension=fileinfo`
- `extension=pdo_sqlite` *(for local SQLite development)*

### Step 2: Install Composer Dependencies
Open your terminal in `c:\xampp\htdocs` and run:
```bash
C:\xampp\php\php.exe C:\xampp\php\composer.phar install
```

### Step 3: Configure `.env` File
Ensure `.env` exists in your project root:
```env
APP_NAME=InternTrack
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite
DB_DATABASE=C:\xampp\htdocs\database\database.sqlite
```

### Step 4: Run Migrations & Seeders
This creates all 17 database tables and seeds them with realistic test accounts, jobs, applications, interviews, bookmarks, and audit logs:
```bash
C:\xampp\php\php.exe artisan migrate:fresh --seed --force
```

### Step 5: Clear Laravel Caches
```bash
C:\xampp\php\php.exe artisan config:clear
C:\xampp\php\php.exe artisan view:clear
C:\xampp\php\php.exe artisan route:clear
```

### Step 6: Start local server
```bash
C:\xampp\php\php.exe artisan serve --host=127.0.0.1 --port=8000
```
Open **[http://127.0.0.1:8000](http://127.0.0.1:8000)** in your browser.

---

## 2. Complete Directory & File Reference (Konsi File Me Kya Hai)

### 🛣️ Routes (`routes/`)
| File Path | Description (Kya Hai Isme) |
|---|---|
| [`routes/web.php`](file:///C:/xampp/htdocs/routes/web.php) | **All Web / Blade UI Routes**. Defines URLs for landing, auth, student dashboard, job discovery, application tracking, company portal, and admin console. |
| [`routes/api.php`](file:///C:/xampp/htdocs/routes/api.php) | **All REST API Routes (`/api/v1/*`)**. Used by React/Next.js/Vue SPAs or mobile apps. Protected via Sanctum Bearer tokens. |

---

### 🎨 Blade Views & UI Layouts (`resources/views/`)

#### Layout Files (`resources/views/layouts/`)
| File Path | Description |
|---|---|
| [`layouts/app.blade.php`](file:///C:/xampp/htdocs/resources/views/layouts/app.blade.php) | **Public Base Layout**. Includes Bootstrap 5 CDN, Google Inter font, `<nav>` header, and `<footer>`. |
| [`layouts/auth.blade.php`](file:///C:/xampp/htdocs/resources/views/layouts/auth.blade.php) | **Authentication Layout**. Dark-themed centered card layout for Login and Register pages. |
| [`layouts/dashboard.blade.php`](file:///C:/xampp/htdocs/resources/views/layouts/dashboard.blade.php) | **Dashboard Layout**. Includes left sidebar navigation + top header + main content area. |

#### Page Views (`resources/views/`)
| File Path | Target URL | Description |
|---|---|---|
| [`welcome.blade.php`](file:///C:/xampp/htdocs/resources/views/welcome.blade.php) | `/` | **Public Landing Page**. Hero banner, value pillars, recent opportunities, featured companies. |
| [`auth/login.blade.php`](file:///C:/xampp/htdocs/resources/views/auth/login.blade.php) | `/login` | **Login Page**. Email & password form with "Remember Me". |
| [`auth/register.blade.php`](file:///C:/xampp/htdocs/resources/views/auth/register.blade.php) | `/register` | **Registration Page**. Supports role selection (`student` vs `company`). |
| [`dashboard/index.blade.php`](file:///C:/xampp/htdocs/resources/views/dashboard/index.blade.php) | `/dashboard` | **Student Dashboard**. Stats (Applications, In Progress, Interviews, Bookmarks), recent opportunities. |
| [`jobs/index.blade.php`](file:///C:/xampp/htdocs/resources/views/jobs/index.blade.php) | `/jobs` | **Job Search**. Filters (job_type, location, sector, is_remote, verified_only), paginated grid. |
| [`jobs/show.blade.php`](file:///C:/xampp/htdocs/resources/views/jobs/show.blade.php) | `/jobs/{id}` | **Job Details Page**. Description, skills, verification badge, Apply Form, Save Bookmark button. |
| [`applications/index.blade.php`](file:///C:/xampp/htdocs/resources/views/applications/index.blade.php) | `/applications` | **My Applications List**. Table of applied jobs with status badges and interview info. |
| [`applications/show.blade.php`](file:///C:/xampp/htdocs/resources/views/applications/show.blade.php) | `/applications/{id}` | **Application Details & Visual Timeline**. Step-by-step status history timeline + scheduled interviews. |
| [`interviews/index.blade.php`](file:///C:/xampp/htdocs/resources/views/interviews/index.blade.php) | `/interviews` | **Interview Schedule**. List of scheduled rounds with dates, interviewer info, and meeting links. |
| [`saved-jobs/index.blade.php`](file:///C:/xampp/htdocs/resources/views/saved-jobs/index.blade.php) | `/saved-jobs` | **Saved Jobs / Bookmarks**. Grid of saved job cards with remove buttons. |
| [`profile/index.blade.php`](file:///C:/xampp/htdocs/resources/views/profile/index.blade.php) | `/profile` | **Student Profile & Resume Vault**. Edit info + Upload new resume versions. |
| [`company/dashboard.blade.php`](file:///C:/xampp/htdocs/resources/views/company/dashboard.blade.php) | `/company/dashboard` | **Company Overview**. Verification banner, stats, recent postings. |
| [`company/verification.blade.php`](file:///C:/xampp/htdocs/resources/views/company/verification.blade.php) | `/company/verification` | **Company Verification**. Submit document URL form + review history log. |
| [`company/jobs/index.blade.php`](file:///C:/xampp/htdocs/resources/views/company/jobs/index.blade.php) | `/company/jobs` | **Job Postings Management**. List of company's job postings with active/inactive toggles. |
| [`company/jobs/create.blade.php`](file:///C:/xampp/htdocs/resources/views/company/jobs/create.blade.php) | `/company/jobs/create` | **Post New Job Form**. Enforces Employer Verification Gate. |
| [`company/jobs/edit.blade.php`](file:///C:/xampp/htdocs/resources/views/company/jobs/edit.blade.php) | `/company/jobs/{id}/edit` | **Edit Job Form**. Update job title, description, deadline, stipend, etc. |
| [`company/applications.blade.php`](file:///C:/xampp/htdocs/resources/views/company/applications.blade.php) | `/company/applications` | **Applicants Manager**. Table of candidates who applied to company's jobs. |
| [`admin/dashboard.blade.php`](file:///C:/xampp/htdocs/resources/views/admin/dashboard.blade.php) | `/admin/dashboard` | **Admin Console Overview**. Platform stat cards, quick action buttons, audit logs. |
| [`admin/verifications.blade.php`](file:///C:/xampp/htdocs/resources/views/admin/verifications.blade.php) | `/admin/verifications` | **Verification Queue**. Inline Approve button + Reject Modal with reason text input. |
| [`admin/companies.blade.php`](file:///C:/xampp/htdocs/resources/views/admin/companies.blade.php) | `/admin/companies` | **All Companies List**. Overview of direct and external companies. |
| [`admin/jobs.blade.php`](file:///C:/xampp/htdocs/resources/views/admin/jobs.blade.php) | `/admin/jobs` | **All Job Listings**. Overview of all job postings across the platform. |
| [`admin/users.blade.php`](file:///C:/xampp/htdocs/resources/views/admin/users.blade.php) | `/admin/users` | **User Directory**. List of all users with roles (`admin`, `company`, `student`). |
| [`admin/logs.blade.php`](file:///C:/xampp/htdocs/resources/views/admin/logs.blade.php) | `/admin/logs` | **Admin Audit Logs**. Complete system audit log table. |

---

## 3. How to Modify Every Frontend File (Kis File Ko Kaise Change Kare)

Here are explicit instructions for customizing or redesigning each part of the frontend:

---

### 🛠️ Modifying Layouts (`resources/views/layouts/`)

#### 1. `resources/views/layouts/app.blade.php` (Public Layout)
- **What it controls**: Global `<head>`, CSS stylesheets, Google Fonts, Navbar inclusion, Flash Alert container, and Footer.
- **How to change styles/fonts**:
  - To change primary colors, edit `:root { --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }` inside `<style>`.
  - To switch from Bootstrap 5 to Tailwind CSS, replace the `<link href="...bootstrap.min.css">` tag with your Tailwind build link.
  - To customize the global footer, edit `<footer class="footer">...</footer>`.

#### 2. `resources/views/layouts/auth.blade.php` (Login/Register Layout)
- **What it controls**: The dark-themed centered card wrapper for authentication pages.
- **How to change**:
  - To change background color/gradient, modify `body { background: linear-gradient(...); }`.
  - To adjust card width or padding, edit `.auth-card { max-width: 480px; padding: 32px; }`.

#### 3. `resources/views/layouts/dashboard.blade.php` (Dashboard Layout)
- **What it controls**: The 2-column sidebar + main content dashboard wrapper.
- **How to change**:
  - To adjust sidebar width, edit `.sidebar { width: 260px; }`.
  - To add a persistent header (e.g. notifications dropdown or global search), edit `<div class="d-flex justify-content-between align-items-center mb-4">...</div>`.

---

### 🧩 Modifying Components (`resources/views/components/`)

#### 1. `resources/views/components/job-card.blade.php` (Job Card)
- **Props**: `:job` (Eloquent Job object).
- **How to customize**:
  - To add new badges (e.g. experience level or salary range), edit:
    ```blade
    @if($job->stipend_salary)
        <div class="fw-semibold text-success small mb-1">...</div>
    @endif
    ```
  - To change how verified companies look, edit the `@if(($job->company->latestVerification->status ?? '') === 'approved')` block.

#### 2. `resources/views/components/status-badge.blade.php` (Status Badge)
- **Props**: `:status` or `:type`.
- **How to customize**:
  - To add a new status color (e.g. `onboarding` or `offer_extended`), edit the `switch ($val)` block in `@php`:
    ```php
    case 'offer_extended':
        $badgeClass = 'bg-success text-white fw-bold';
        break;
    ```

#### 3. `resources/views/components/navbar.blade.php` (Top Navigation Bar)
- **How to customize**:
  - To add new public nav links (e.g. "About Us" or "Blog"), add an `<li>` inside `<ul class="navbar-nav me-auto ms-4">`.
  - To change logo/branding, edit `<a class="navbar-brand">`.

#### 4. `resources/views/components/sidebar.blade.php` (Dashboard Sidebar)
- **How to customize**:
  - To add a new menu item for Students (e.g., "Mock Tests"), edit `@if(auth()->user()->isStudent())` and add:
    ```blade
    <li class="nav-item mb-1">
        <a href="/mock-tests" class="nav-link text-white {{ request()->is('mock-tests*') ? 'active bg-primary' : '' }}">
            <i class="bi bi-journal-code me-2"></i> Mock Tests
        </a>
    </li>
    ```
  - Similarly, add links under `@elseif(auth()->user()->isCompany())` or `@elseif(auth()->user()->isAdmin())`.

---

### 📄 Modifying Page Views (`resources/views/`)

#### 1. `welcome.blade.php` (Landing Page)
- **How to customize**:
  - Edit the `.hero-banner` section to update headline text, subtext, or CTA buttons.
  - To change value pillar icons, edit `<div class="display-6 text-primary mb-3"><i class="bi bi-compass"></i></div>`.

#### 2. `jobs/index.blade.php` (Job Discovery & Filters)
- **How to add a new search filter**:
  1. Add an `<input>` or `<select>` in the `<form action="/jobs" method="GET">` filter card.
  2. Handle the query parameter in [`app/Http/Controllers/Web/WebJobController.php`](file:///C:/xampp/htdocs/app/Http/Controllers/Web/WebJobController.php) inside `index()`.

#### 3. `jobs/show.blade.php` (Job Details & Apply Form)
- **How to customize**:
  - To add extra fields to the apply form (e.g. "Expected Joining Date"), add an `<input name="joining_date">` inside `<form action="/jobs/{{ $job->id }}/apply" method="POST">`.
  - Ensure the field is accepted in `WebJobController::applyJob()`.

#### 4. `applications/show.blade.php` (Application Visual Timeline)
- **How to customize timeline design**:
  - The step timeline is generated from `@forelse($application->statusHistory as $history)`.
  - To change line color or dot icons, edit `<div class="position-absolute ... style="left: -25px; ...">`.
  - To add candidate action buttons (e.g. "Accept Offer"), add a conditional button block when `$application->status === 'selected'`.

#### 5. `profile/index.blade.php` (Student Profile & Resume Vault)
- **How to customize**:
  - To add extra student profile fields (e.g. GitHub URL, LinkedIn URL), add inputs inside `<form action="/profile" method="POST">`.
  - To update resume versioning UI, edit `<div class="card card-custom bg-white p-4">` under Resume Vault.

#### 6. `company/jobs/create.blade.php` (Post Job Form)
- **How to customize**:
  - Add form inputs for new job attributes.
  - Ensure `CompanyPortalController::storeJob()` handles the new inputs.

#### 7. `admin/verifications.blade.php` (Verification Queue)
- **How to customize**:
  - Edit the **Approve** button or **Reject Modal** HTML.
  - Modify `WebAdminController::approveVerification()` or `rejectVerification()` if adding extra admin review fields.

---

### ⚡ Building a Decoupled React / Next.js SPA

If you prefer building a separate React, Next.js, or Vue frontend instead of Blade:

1. **Keep the Laravel Backend Running**: Serving API at `http://127.0.0.1:8000/api/v1/`
2. **Use Existing TypeScript Types**: Look at [`frontend/src/types/`](file:///C:/xampp/htdocs/frontend/src/types/) for pre-defined TypeScript interfaces (`job.ts`, `auth.ts`, `application.ts`).
3. **CORS Configuration**: Edit [`config/cors.php`](file:///C:/xampp/htdocs/config/cors.php) to allow your frontend origin (e.g. `http://localhost:3000`).
4. **API Requests with Axios**:
   ```typescript
   import axios from 'axios';

   const api = axios.create({
     baseURL: 'http://127.0.0.1:8000/api/v1',
     headers: {
       'Accept': 'application/json',
       'Content-Type': 'application/json',
     },
   });

   // Attach Sanctum Bearer token from localStorage
   api.interceptors.request.use((config) => {
     const token = localStorage.getItem('token');
     if (token) {
       config.headers.Authorization = `Bearer ${token}`;
     }
     return config;
   });

   export default api;
   ```

---

## 4. Authentication Architecture (Web Session vs API Token)

### Web Session Mode (Blade UI)
- Standard cookies managed by browser.
- Configured in [`bootstrap/app.php`](file:///C:/xampp/htdocs/bootstrap/app.php).
- Unauthenticated requests to protected web routes redirect to `/login` with a flash error message.

### REST API Bearer Token Mode (Decoupled Frontend)
- Send requests to `/api/v1/auth/login` with `email` and `password`.
- Receive a Sanctum Bearer Token in response.
- Attach `Authorization: Bearer <token>` header to all subsequent API requests.

---

## 5. Role-Based Access & Landing Routes

```
                          ┌─────────────────────────────┐
                          │   User Signs In / Register  │
                          └──────────────┬──────────────┘
                                         │
                 ┌───────────────────────┼───────────────────────┐
                 ▼                       ▼                       ▼
           role === 'student'      role === 'company'      role === 'admin'
                 │                       │                       │
                 ▼                       ▼                       ▼
            /dashboard          /company/dashboard       /admin/dashboard
       (Applications, Saved,    (Job Postings, Applicants,  (Verifications, Users,
        Interviews, Resumes)     Org Verification)        Jobs, Audit Logs)
```

---

## 6. Complete REST API Specification (`/api/v1/*`)

All API JSON responses are wrapped in a standard envelope:
```json
{
  "success": true,
  "message": "Operation description",
  "data": { ... }
}
```

### Key API Endpoints List

#### Auth
- `POST /api/v1/auth/login` — Login user (returns Sanctum token)
- `POST /api/v1/auth/register` — Register new student/company
- `GET  /api/v1/auth/me` — Get current user profile
- `POST /api/v1/auth/logout` — Revoke token

#### Jobs
- `GET  /api/v1/jobs` — Search jobs (`keyword`, `job_type`, `location`, `sector`, `is_remote`, `verified_only`, `per_page`)
- `GET  /api/v1/jobs/{id}` — Get single job details

#### Applications & Timelines
- `POST /api/v1/applications` — Submit job application
- `GET  /api/v1/students/me/applications` — List candidate applications
- `GET  /api/v1/applications/{id}` — Get application details + `status_history` + `interviews`
- `POST /api/v1/applications/{id}/status` — Update application status (`shortlisted`, `interview`, `selected`, `rejected`)

#### Company & Admin
- `POST /api/v1/companies/{id}/verification` — Submit verification request
- `POST /api/v1/company/jobs` — Post new direct job (*verification gate enforced*)
- `POST /api/v1/admin/company-verifications/{id}/approve` — Approve verification
- `POST /api/v1/admin/company-verifications/{id}/reject` — Reject verification with remarks
- `POST /api/v1/admin/ingest-external-jobs` — Trigger external job aggregator

---

## 7. Frontend Business Rules & Logic Constraints

### 🟢 Employer Verification Gate
- Unverified companies (`company.is_verified === false`) **cannot post jobs**.
- Frontend UI should disable the "Post Job" form and prompt the user to visit `/company/verification`.
- Display a `🟢 Verified` badge on job cards if `job.company.is_verified === true`.

### 📊 Application Status Visual Timeline
- Status steps: `applied` $\rightarrow$ `shortlisted` $\rightarrow$ `interview` $\rightarrow$ `selected` / `rejected`.
- Frontend UI should render a vertical step timeline showing timestamps and reviewer notes from `status_history`.
- Display scheduled interview rounds with meeting links (`location_meeting_link`).

### 📌 Direct vs External Job Badges
- Direct Jobs (`job.source.type === 'direct'`): Show `Source: InternTrack` badge. Applications are submitted within the platform.
- External Jobs (`job.source.type === 'external'`): Show `Source: LinkedIn / Internshala` badge. Include an external link button to `job.job_url`.

---

## 8. Seeded Test Accounts & Passwords

All accounts use the password: **`password`**

| Role | Name | Email | Password | Pre-populated Test Data |
|---|---|---|---|---|
| 🛡️ **Admin** | Platform Admin | `admin@interntrack.dev` | `password` | Access to approvals queue, user directory, audit logs |
| 🏢 **Company (Verified)** | Nexus Innovations | `company@interntrack.dev` | `password` | Verified employer profile, active job listings, candidate applications |
| 🏢 **Company (Pending)** | Starlight Digital | `hr@starlight.com` | `password` | Pending verification request status |
| 🏢 **Company (Rejected)** | Phantom Ventures | `jobs@phantom.co` | `password` | Rejected verification status with admin remarks |
| 🏢 **Company (Unverified)**| GreenPath NGO | `info@greenpath.org` | `password` | Unverified company profile |
| 🎓 **Student (Primary)** | Aarav Sharma | `student@interntrack.dev` | `password` | 4 applications with status timelines, 2 interview rounds, 4 saved job bookmarks, 2 resumes |
| 🎓 **Student** | Priya Patel | `priya@university.edu` | `password` | M.Tech AI candidate profile with fellowship application |
| 🎓 **Student** | Rahul Mehta | `rahul@college.ac.in` | `password` | MCA candidate profile |

---

## 9. Troubleshooting & Common Commands

### Re-seeding the Database Fresh
```bash
C:\xampp\php\php.exe artisan migrate:fresh --seed --force
```

### Clearing Caches
```bash
C:\xampp\php\php.exe artisan config:clear
C:\xampp\php\php.exe artisan view:clear
C:\xampp\php\php.exe artisan route:clear
C:\xampp\php\php.exe artisan cache:clear
```

### Checking All Registered Routes
```bash
C:\xampp\php\php.exe artisan route:list
```

---

*For backend schema changes or new API additions, refer to `routes/api.php` or `app/Http/Controllers/Api/V1/*`.*
