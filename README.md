# MediFlow

MediFlow is a comprehensive healthcare management system designed to streamline staff, patient, appointment, and equipment management. Built with a robust MVC architecture in PHP, it provides a secure, role-based platform for healthcare facilities.

## Key Features

- **Role-Based Access Control (RBAC):** Distinct interfaces and permissions for Administrators, Patients, Technicians, Pharmacists, and Editors.
- **Admin Dashboard:** Centralized overview with statistics, recent activity, and user management capabilities.
- **User Management:** Full CRUD operations for system users with status toggling (suspend/activate).
- **Secure Authentication:** Passwords hashed with bcrypt, Google OAuth integration, secure password resets, and reCAPTCHA protection.
- **Real-time Notifications:** Admin alert system for new registrations, password updates, and account status changes.
- **Equipment & Medical Magazine Modules:** Dedicated modules for inventory tracking, equipment reservations, and publishing medical articles.
- **Responsive UI:** Modern, accessible interface styled with Tailwind CSS, featuring interactive elements and smooth transitions.

## Tech Stack

- **Backend:** PHP 7.4+ (Custom MVC Architecture)
- **Database:** MySQL / MariaDB (PDO)
- **Frontend:** HTML5, JavaScript, Tailwind CSS
- **Integrations:** Google OAuth 2.0, Google reCAPTCHA
- **Libraries:** Intro.js (for user onboarding tours)

## Project Structure

```
MediFlow/
├── Controllers/       # Application business logic and routing handlers
├── Models/            # Database interactions and data structures
├── Views/             # UI templates (Front-office and Back-office)
├── Core/              # Core framework components (Router, SessionHelper, NotificationService)
├── assets/            # Static files (CSS, JS, images, icons)
├── config/            # Additional configuration files
├── index.php          # Application entry point
├── config.php         # Database and environment configuration
└── mediflowFinal.sql  # Database schema and initial seed data
```

## Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL or MariaDB
- Web server (Apache with `mod_rewrite` enabled is recommended)
- Composer (for dependency management)

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/Mediflow.git
   cd Mediflow
   ```

2. **Environment Configuration:**
   - Copy `.env.example` to `.env` and fill in your API keys (Google OAuth, reCAPTCHA, etc.).
   - Configure your database credentials in `config.php`.

3. **Database Setup:**
   - Create a new MySQL database named `mediflow`.
   - Import the provided SQL dump:
     ```bash
     mysql -u root -p mediflow < mediflowFinal.sql
     ```

4. **Web Server Configuration:**
   - Point your local web server (e.g., XAMPP) to the project root directory.
   - Ensure the Apache `mod_rewrite` module is enabled to allow the `.htaccess` routing rules to function.
   - Access the application at `http://localhost/Integration` (or your configured virtual host).

## Default Credentials

For initial setup and testing, an administrator account is provided:
- **Email:** admin@mediflow.com
- **Password:** admin123

*Please change these credentials immediately after deployment.*

## Security Features

- Protection against SQL Injection using PDO Prepared Statements.
- Cross-Site Scripting (XSS) prevention via output escaping (`htmlspecialchars`).
- Sensitive data separation using environment variables (`.env`).
- Session hijacking mitigation protocols.

---
**Version:** 2.0.0 | **License:** Proprietary
