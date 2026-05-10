# MediFlow - Quick Setup Guide (XAMPP)

## 🚀 5-Minute Setup

### Prerequisites (Download & Install)
1. **XAMPP** → https://www.apachefriends.org
   - Includes PHP, Apache, MySQL all-in-one
   - Install to default location (C:\xampp)

2. **Git** (optional but recommended)
   - https://git-scm.com
   - For cloning/pulling project updates

---

## Step-by-Step Setup

### ✅ Step 1: Extract Project
```
C:\xampp\htdocs\integration\
                ├── config.php
                ├── index.php
                ├── Controllers/
                ├── Models/
                ├── Views/
                └── ... (all other files)
```

### ✅ Step 2: Start Services
```
1. Open: C:\xampp\xampp-control.exe
2. Click "Start" next to Apache
3. Click "Start" next to MySQL
4. Wait for "Running" status (green)
```

### ✅ Step 3: Create Database
```
1. Open: http://localhost/phpmyadmin
2. Left sidebar → Click "New"
3. Database name: mediflow
4. Collation: utf8mb4_unicode_ci
5. Click "Create"

6. Now in mediflow database:
   - Click "Import" tab
   - Click "Choose File"
   - Select: mediflowFinal.sql
   - Click "Import"
   - Wait for success message
```

### ✅ Step 4: Configure Database Connection
Edit: `C:\xampp\htdocs\integration\config.php`

Find the database credentials section:
```php
private static function getDatabaseConfig(): array {
    return [
        'host' => 'localhost',      // ← Keep as-is
        'db' => 'mediflow',         // ← Keep as-is
        'user' => 'root',           // ← Keep as-is (default XAMPP user)
        'password' => '',           // ← Keep empty (XAMPP default)
        'charset' => 'utf8mb4'      // ← Keep as-is
    ];
}
```

### ✅ Step 5: Add OpenRouter API Key (Optional but Recommended)
Edit: `C:\xampp\htdocs\integration\config.php`

Find this function:
```php
private static function getOpenRouterApiKey(): string {
    return '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';
                          // ↑ This is the current key (may expire)
                          // Get your own: https://openrouter.ai
}
```

Steps:
1. Go to https://openrouter.ai
2. Sign up (free)
3. Go to API Keys section
4. Create new key
5. Copy it
6. Paste in config.php (replace the value in quotes)

### ✅ Step 6: Set File Permissions (Windows)
```
1. Right-click: C:\xampp\htdocs\integration\assets\uploads
2. Properties → Security → Edit
3. Select "Users"
4. Check: Modify, Write
5. Apply → OK

6. Same for: C:\xampp\htdocs\integration\data
```

### ✅ Step 7: Test the Installation
```
1. Open browser: http://localhost/integration/index.php
   OR: http://localhost/integration/

2. You should see login page

3. Try logging in:
   Email: admin11@mediflow.com
   Password: (check your database or ask admin)

4. If it works → You're all set! 🎉
```

---

## 🔍 Troubleshooting

### Issue: "Cannot connect to database"
**Solution:**
1. Make sure MySQL is running (green "Running" status)
2. Check database credentials in config.php
3. Run: phpMyAdmin → check if `mediflow` database exists

### Issue: "404 Page Not Found"
**Solution:**
```
1. Make sure Apache is running (green "Running" status)
2. Check URL: http://localhost/integration/
3. Clear browser cache: Ctrl+Shift+Delete
4. Restart Apache: Click "Stop" then "Start"
```

### Issue: "File upload not working"
**Solution:**
1. Right-click `assets/uploads` → Properties → Security
2. Allow "Write" permission for your user
3. Restart Apache

### Issue: "Chatbot not responding"
**Solution:**
1. Check if OpenRouter API key is correct in config.php
2. Internet connection working?
3. Open browser console (F12) → Check for errors
4. Check network tab to see if API request is being made

### Issue: "Module 'pdo_mysql' not found"
**Solution:**
```
1. Find: C:\xampp\php\php.ini
2. Search for: ;extension=pdo_mysql
3. Remove the semicolon: extension=pdo_mysql
4. Save file
5. Restart Apache
```

---

## 📊 Database Verification

After importing SQL, verify tables exist:
```
1. Open: http://localhost/phpmyadmin
2. Click database: mediflow
3. You should see tables:
   ✓ utilisateurs
   ✓ equipement
   ✓ reservation
   ✓ rendez_vous
   ✓ consultation
   ✓ contact
   ✓ planning
   ✓ post
   ✓ comment
   ✓ post_likes
   ✓ password_resets
```

---

## 🔐 First Time Setup

### Create Admin User (if needed)
```
1. Open phpMyAdmin
2. Click database: mediflow
3. Click table: utilisateurs
4. Click "Insert"
5. Fill in:
   - id_PK: (auto-increment, leave blank)
   - matricule: ADM001
   - nom: Admin
   - prenom: User
   - mail: admin@example.com
   - tel: 0600000000
   - adresse: Your Address
   - id_role: (check role id from roles table)
   - created_at: (current date)
   
6. Click "Go"
```

---

## 📱 Access Points

| URL | Purpose |
|-----|---------|
| `http://localhost/integration/` | Main application |
| `http://localhost/phpmyadmin/` | Database management |
| `http://localhost/integration/admin` | Admin panel |
| `http://localhost/integration/dashboard` | User dashboard |

---

## 🎯 Next Steps

1. ✅ Get all group members set up using this guide
2. ✅ Test login with your credentials
3. ✅ Navigate dashboard to verify everything works
4. ✅ Test file upload (profile picture)
5. ✅ Test chatbot (if API key added)
6. ✅ Test equipment reservation
7. ✅ Ready to deploy! 🚀

---

## 💡 Tips

- **Save passwords** in a secure location (password manager)
- **Backup database** regularly: Export from phpMyAdmin
- **Keep API keys secret** - don't commit to Git
- **Test thoroughly** before sharing with users
- **Document changes** - keep track of modifications

---

**Questions? Check DEPENDENCIES.md for detailed information!**
