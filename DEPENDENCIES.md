# MediFlow - Project Dependencies & Setup Guide

## 📋 System Requirements

### Server Requirements
- **PHP**: 7.4 or higher (8.0+ recommended)
- **MySQL/MariaDB**: 5.7+ or 10.3+
- **Web Server**: Apache with `mod_rewrite` enabled (XAMPP includes this)
- **Operating System**: Windows, macOS, or Linux

### Browser Requirements
- Modern browser with ES6 JavaScript support
- Chrome, Firefox, Edge, or Safari (latest versions)

---

## 🔧 Backend Dependencies

### PHP Extensions (must be enabled in php.ini)
```
✓ PDO (PHP Data Objects)
✓ pdo_mysql (MySQL driver for PDO)
✓ cURL (for API requests to OpenRouter)
✓ mbstring (for string handling)
✓ json (for JSON encoding/decoding)
```

**How to verify:**
```bash
# In VS Code terminal or command prompt
php -m | grep -i "pdo\|curl\|mbstring"
```

### PHP Configuration (in php.ini)
```ini
; Recommended settings
post_max_size = 50M
upload_max_filesize = 50M
max_execution_time = 300
memory_limit = 256M
```

---

## 🌐 Frontend Dependencies (CDN-based - no installation needed)

### CSS Frameworks & Icons
| Library | Version | Purpose |
|---------|---------|---------|
| **Tailwind CSS** | Latest | Utility-first CSS framework |
| **Material Symbols** | Google Fonts | Icon library |
| **Manrope Font** | Google Fonts | Custom typography |
| **Inter Font** | Google Fonts | Secondary typography |

### JavaScript Libraries
| Library | Version | Purpose | Source |
|---------|---------|---------|--------|
| **Intro.js** | 7.2.0 | Guided onboarding tour | CDN (cdnjs) |

All loaded via CDN - **no npm/node required!**

---

## 🗄️ Database Setup

### Required Database
- **Name**: `mediflow` (or as configured in config.php)
- **Charset**: UTF-8 (utf8mb4)
- **Collation**: utf8mb4_unicode_ci

### Database Tables
All tables are defined in `mediflowFinal.sql`:
```
Tables: utilisateurs, equipement, reservation, consultation, 
        rendez_vous, post, comment, contact, planning, post_likes, 
        password_resets, etc.
```

### Setup Steps
1. Create MySQL database: `mediflow`
2. Import SQL file:
   ```bash
   # Using command line
   mysql -u root -p mediflow < mediflowFinal.sql
   
   # Or using phpMyAdmin
   # Upload mediflowFinal.sql in Import tab
   ```

---

## 🔑 External API Keys

### OpenRouter API (for Chatbot)
**Purpose**: AI chatbot using Gemma model (free tier)

**Setup**:
1. Sign up at [OpenRouter.ai](https://openrouter.ai)
2. Get your API key
3. Add to `config.php`:
   ```php
   private static function getOpenRouterApiKey() {
       return 'sk-or-v1-YOUR_API_KEY_HERE';
   }
   ```

**Cost**: Free tier available (no payment card required)

---

## 📁 File Structure Dependencies

```
integration/
├── config.php                    # Main configuration
├── config/
│   └── database.php             # Database connection
├── Core/
│   ├── App.php                  # Router & MVC handler
│   ├── Autoloader.php           # PSR-4 autoloader
│   └── SessionHelper.php        # Session management
├── Controllers/                 # ~20+ controller files
├── Models/                      # ~10+ model files
├── Views/
│   ├── Back/                   # Admin templates
│   ├── Front/                  # Public templates
│   └── components/             # Reusable components
├── assets/
│   ├── css/                    # Custom stylesheets
│   ├── js/                     # Custom scripts
│   ├── images/                 # App images
│   └── uploads/                # User uploads (profile pics)
├── api/                        # API endpoints
│   ├── gemini-chat.php        # Chatbot API proxy
│   ├── upload-profile-pic.php # File upload handler
│   ├── catalogue-content.php  # Equipment catalogue
│   ├── dashboard-content.php  # Dashboard content
│   ├── reservations-content.php
│   └── profile-content.php
└── .htaccess                   # URL routing rules
```

---

## 📦 Installation Checklist

### Step 1: Server Setup
- [ ] PHP 7.4+ installed
- [ ] MySQL/MariaDB running
- [ ] Apache with mod_rewrite enabled
- [ ] Project in `c:\xampp\htdocs\integration` (or accessible via localhost)

### Step 2: Database Setup
- [ ] Create `mediflow` database
- [ ] Import `mediflowFinal.sql`
- [ ] Verify tables exist in phpMyAdmin

### Step 3: Configuration
- [ ] Edit `config.php` with database credentials
- [ ] Add OpenRouter API key to `getOpenRouterApiKey()`
- [ ] Verify paths in Config class

### Step 4: Permissions
- [ ] Ensure `assets/uploads/` is writable (for profile pictures)
- [ ] Ensure `data/` folder is writable (for demandes.json)
- [ ] File permissions: 755 for directories, 644 for files

### Step 5: Testing
- [ ] Access `http://localhost/integration/index.php`
- [ ] Login with test user (email: admin11@mediflow.com)
- [ ] Verify database connection in profile pic upload
- [ ] Test chatbot functionality

---

## 🚀 Quick Start Commands

### Using XAMPP (Windows)
```bash
# Start XAMPP
- Double-click xampp-control.exe
- Click Start next to Apache
- Click Start next to MySQL
- Open http://localhost/integration/
```

### Using Command Line
```bash
# Test PHP
php -v

# Test MySQL connection
mysql -u root -p mediflow

# Start local server (if not using XAMPP)
php -S localhost:8000
# Then visit http://localhost:8000/integration/
```

---

## 🐛 Common Issues & Solutions

### "Module 'pdo_mysql' not found"
- Enable in `php.ini`: Uncomment `extension=pdo_mysql`
- Restart Apache

### "CORS Error" (Chatbot not working)
- OpenRouter API key not set in config.php
- Network connection issue
- Check browser console for exact error

### "File upload fails"
- Check `assets/uploads/` permissions
- Ensure PHP has write access
- Check `upload_max_filesize` in php.ini

### "404 Error" on all pages
- `.htaccess` not working - enable mod_rewrite
- Wrong project path - should be in `htdocs/integration/`
- Clear browser cache

---

## 📞 Support

For issues with setup, check:
1. `error_log` in project root
2. Apache error logs: `xampp/apache/logs/error.log`
3. MySQL error logs: `xampp/mysql/data/mysql_error.log`
4. Browser console (F12 → Console tab)

---

## ✅ Verification

Run this to verify all dependencies:

```bash
# 1. Check PHP version
php -v

# 2. Check required extensions
php -m | grep -E "PDO|curl|mbstring|json"

# 3. Check MySQL
mysql -u root -p -e "SHOW DATABASES;" | grep mediflow

# 4. Test file permissions
ls -la assets/uploads/
ls -la data/

# 5. Check API connectivity (in browser console)
fetch('https://openrouter.ai/api/v1').then(r => r.text()).catch(e => console.error(e))
```

---

**All set? You're ready to deploy!** 🎉
