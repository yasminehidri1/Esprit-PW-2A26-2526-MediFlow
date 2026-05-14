# MediFlow - Healthcare Management System

## Overview
This project was developed as part of the PW – 2nd Year Program at **Esprit School of Engineering** (Academic Year 2025–2026).
It consists of a comprehensive healthcare management system designed to streamline staff, patient, appointment, and equipment management. Built with a robust MVC architecture in PHP, it provides a secure, role-based platform for healthcare facilities.

## Features
- **Role-Based Access Control (RBAC):** Distinct interfaces and permissions for Administrators, Patients, Technicians, Pharmacists, and Editors.
- **Admin Dashboard:** Centralized overview with statistics, recent activity, and user management capabilities.
- **User Management & Onboarding:** Full CRUD operations for system users with status toggling (suspend/activate), plus an interactive intro.js guided tour for new patients.
- **Secure Authentication:** Passwords hashed with bcrypt, Google OAuth integration, secure password resets, and reCAPTCHA protection.
- **Real-time Notifications:** Context-aware, isolated alert systems ensuring Admins see system-wide events and Users only see notifications relevant to them.
- **Equipment & Medical Magazine Modules:** Dedicated modules for inventory tracking, equipment reservations, and publishing medical articles.
- **Responsive UI:** Modern, accessible interface styled with Tailwind CSS, featuring interactive elements and smooth transitions.

## Tech Stack
### Frontend
- HTML5
- Vanilla JavaScript
- Tailwind CSS

### Backend
- PHP 7.4+
- MySQL / MariaDB (PDO)
- Integrations: Google OAuth 2.0, Google reCAPTCHA
- Libraries: Intro.js, PHPMailer, Composer

## Architecture
Built with a custom MVC Architecture:
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

## Contributors
- **[Fathi Khelifi]** - [GitHub](https://github.com/fathikhelifi69) | [LinkedIn](https://www.linkedin.com/in/khelifi-fathi-449b5835b/)
- **[Nada Karoui]** - [GitHub](https://github.com/nadakaroui) | [LinkedIn](https://www.linkedin.com/in/nada-karoui-98a902332/)
- **[Yasmine Hidri]** - [GitHub](https://github.com/yasminehidri1) | [LinkedIn](https://www.linkedin.com/in/hidri-yasmine-1b6709328/)
- **[eya malouche]** - [GitHub](https://github.com/eyam22) | [LinkedIn](https://www.linkedin.com/in/eya-malouche-538b20359/)
- **[khalil cherif]** - [GitHub](https://github.com/rymela) | [LinkedIn](https://linkedin.com/in/votre-lien-linkedin)
- **[Mohamed Mehdi Berriri]** - [GitHub](https://github.com/Mehdi-Berriri) | [LinkedIn](https://linkedin.com/in/votre-lien-linkedin)
## coach 
- **[ghada ben khalifa]** - [GitHub](https://github.com/BenKhalifaGHADA) | [LinkedIn](https://www.linkedin.com/in/ghada-ben-khalifa-8925b472/)

## Academic Context
Developed at **Esprit School of Engineering – Tunisia**  
PW – 2A26 | 2025–2026

## Getting Started

### Prerequisites
- PHP 7.4 or higher
- MySQL or MariaDB
- Web server (Apache with `mod_rewrite` enabled is recommended)
- Composer (for dependency management)

### Installation
1. **Clone the repository:**
   ```bash
   git clone https://github.com/yasminehidri1/Esprit-PW-2A26-2526-MediFlow.git
   cd Esprit-PW-2A26-2526-MediFlow
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
   - Access the application at your configured virtual host or localhost.

### Default Credentials
For initial setup and testing, an administrator account is provided:
- **Email:** admin11@gmail.com
- **Password:** fathi2004
*(Please change these credentials immediately after deployment.)*

## Acknowledgments
We would like to express our gratitude to our supervisors mdm ghada ben khalifa   and the teaching staff at **Esprit School of Engineering** for their guidance and support throughout the development of this project.
