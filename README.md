# 🚀 PM Control Center

**PM Control Center** is a modern, internal SaaS-style Project Management application designed to orchestrate projects, tasks, risks, milestones, and stakeholders from a single, unified dashboard. 

It features a **Dual-View Architecture** (Global View for PMOs and Local Project View for specific project tracking) and utilizes a sleek **Bento Box UI** design system for maximum data density and visual hierarchy.

## 🛠 Tech Stack

This project is built using the modern monolithic SPA approach:
- **Backend:** [Laravel 11](https://laravel.com/) (PHP 8.2+)
- **Frontend:** [Vue.js 3](https://vuejs.org/) (Composition API)
- **SPA Routing:** [Inertia.js](https://inertiajs.com/)
- **Styling:** [Tailwind CSS](https://tailwindcss.com/)
- **Route Helpers:** [Ziggy](https://github.com/tighten/ziggy)
- **Database:** MySQL

## 📦 Core Modules

- **Dashboard:** Interactive KPIs and Bento Box widgets.
- **Project Management:** Projects, Tasks, Milestones, Risks, Issues, and Change Requests.
- **Stakeholders:** Clients, Vendors, and Internal Team Management.
- **Activities:** Meeting Schedules and System Activity Logs.

---

## 💻 Getting Started (Local Setup Guide)

Follow these instructions to clone, set up, and run the project on your local machine for development.

### Prerequisites

Make sure you have the following installed on your system:
- **PHP** >= 8.2
- **Composer**
- **Node.js** & **NPM**
- **MySQL** (or any preferred database)
- **Git**

### Step-by-Step Installation

**1. Clone the Repository**

```bash
git clone https://github.com/fikalalif/pm-control-center.git
cd pm-control-center
```

**2. Install PHP Dependencies**

```bash
composer install
```

**3. Install Frontend Dependencies**

```bash
npm install
```

**4. Set Up Environment Variables**

Duplicate the `.env.example` file to create your local `.env` configuration.

```bash
cp .env.example .env
```

Open the `.env` file and update your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pm_control_center
DB_USERNAME=root
DB_PASSWORD=your_password
```

**5. Generate Application Key**

```bash
php artisan key:generate
```

**6. Run Migrations & Seeders**

This project includes a comprehensive relational seeder to populate the database with realistic test data (Projects, Users, Tasks, Risks, etc.).

```bash
php artisan migrate:fresh --seed
```

**7. Start the Development Servers**

Since this is a Laravel + Inertia.js project, you need to run **two** terminal instances simultaneously.

**Terminal 1 (Backend - PHP):**

```bash
php artisan serve
```

**Terminal 2 (Frontend - Vite/Vue):**

```bash
npm run dev
```

Your application is now live! Open your browser and visit: `http://localhost:8000`

---

## 💡 Development Notes

- **Ziggy Routes:** If you add new routes in `routes/web.php` and they do not reflect in the Vue components (e.g., Inertia `<Link>` fails), clear the route cache:
  ```bash
  php artisan optimize:clear
  ```
- **Case Sensitivity:** This project is developed on an Ubuntu (Linux) environment. Ensure your Vue component filenames and paths perfectly match the casing defined in `Inertia::render()` (e.g., `ChangeRequests` vs `changeRequests`).
