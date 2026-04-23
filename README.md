# MediFlow 

A healthcare management system I built to make managing staff, appointments, and medical stuff way easier. It's got a pretty clean admin dashboard, smooth animations, and actually works without crashing (most of the time 😄).

## What's Inside

- **User Management**: Add/edit/delete staff with different roles - admin, doctors, pharmacists, etc
- **Admin Dashboard**: See what's happening at a glance with charts and stats
- **Login System**: Secure authentication so people can't just wander into the admin panel
- **Responsive Design**: Works on your phone, tablet, or desktop
- **Nice Animations**: Pages don't just pop up, they fade in smoothly
- **Role-Based Access**: Admins can do admin stuff, doctors can do doctor stuff, you get the idea

## Built With

- PHP (the real stuff, not just script tags)
- MySQL for the database
- Tailwind CSS so I don't have to write a million lines of CSS
- Just vanilla JavaScript - no bloated frameworks here
- Google Material icons because they're actually pretty good

## How It's Organized

```
Mediflow/
├── Controllers/              # Where the logic lives
│   ├── AdminController.php   # Admin stuff
│   ├── AuthController.php    # Login/register
│   ├── DashboardController.php
│   └── LandingController.php
├── Models/                   # Database stuff happens here
│   ├── UserModel.php
│   └── DashboardModel.php
├── Views/                    # HTML templates
│   ├── Back/                # Admin pages
│   ├── Front/               # Public pages
│   └── layout/              # Header, footer, etc
├── Core/                     # The framework skeleton
├── assets/                   # CSS, JS, images
├── config.php               # Database config
├── index.php                # Entry point
└── .htaccess               # URL routing magic
```

## Getting Started

### You'll Need
- PHP 7.4+ (newer is better)
- MySQL or MariaDB
- Apache with mod_rewrite (usually comes with XAMPP)
- XAMPP is easiest if you're on Windows

### Setup

1. **Clone it**
```bash
git clone https://github.com/yourusername/Mediflow.git
cd Mediflow
```

2. **Fix the database connection**
   - Open `config.php` and update these:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'mediflow');
```

3. **Create the database**
```sql
CREATE DATABASE mediflow;
```

4. **Put the files in the right place**
   - If using XAMPP: `C:\xampp\htdocs\Mediflow`
   - Then go to `http://localhost/Mediflow`

That's it! You should see the login page.

## Login Credentials

First time? Use these:
- **Email**: admin@mediflow.com
- **Password**: admin123

## What You Can Do

**In the Admin Panel:**
- See how many users are in the system
- Add new staff members
- Edit user info
- Delete users (scary button in red)
- Check who logged in recently

**As a Regular User:**
- Log in with your credentials
- See your dashboard
- Update your info (coming soon: profile pictures!)

**Authentication:**
- Sign up for a new account
- Log in securely
- Sessions keep you logged in

## Design & Animations

The UI has some nice touches:
- Buttons scale up when you hover over them
- Pages fade in smoothly
- Tables have this cool shimmer effect on row hover
- Icons spin and rotate
- The logout button looks angry in red (intentional!)
- Everything adapts to your screen size

Works great on desktop, tablet, and phone.

## Security Stuff

- Passwords are hashed (not stored as plain text, obviously)
- SQL injection protection with prepared statements
- Session management so hackers can't just walk in
- Different user roles with different permissions
- Ready for anti-CSRF tokens if needed

## Database

The main table stores user info:

```sql
CREATE TABLE utilisateurs (
  id_PK INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(100),
  prenom VARCHAR(100),
  mail VARCHAR(100) UNIQUE,
  tel VARCHAR(20),
  adresse TEXT,
  motdp VARCHAR(255),
  id_role INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

Each user has a role (admin, doctor, receptionist, etc)
---

**Version**: 1.0.0 | Last update: April 2026

