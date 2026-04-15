# MediFlow Magazine Setup Guide

## Database Setup

The application requires a MySQL/MariaDB database with the following tables:
- `roles` - User role definitions
- `utilisateurs` - User accounts
- `posts` - Magazine articles
- `comments` - Article comments

### Quick Setup

1. **Import the SQL schema** to your `mediflow` database:
   ```bash
   mysql -h 127.0.0.1 -u root mediflow < sql/magazine.sql
   ```

2. **Or run the initialization script** through your web browser:
   - Navigate to: `http://localhost/mediflow/init_tables.php`
   - This will create all missing tables and insert sample data

3. **Verify the setup** by running the diagnostic:
   - Navigate to: `http://localhost/mediflow/debug_db.php`
   - Should show all tables as "EXISTS"

## Project Structure

```
mediflow/
├── config/
│   └── database.php          # PDO database singleton
├── models/
│   ├── Post.php             # Article model (CRUD, likes, views)
│   └── Comment.php          # Comment model (CRUD, moderation)
├── controllers/
│   ├── PostController.php   # Article & like logic
│   └── CommentController.php# Comment & moderation logic
├── views/
│   ├── frontOffice/         # Public-facing templates
│   └── backOffice/          # Admin panel templates
├── assets/
│   ├── css/style.css        # Tailwind CSS styling
│   └── js/frontOffice.js    # AJAX functionality
├── frontOffice.php          # Public router
├── backOffice.php           # Admin router
└── sql/
    └── magazine.sql         # Database schema
```

## Features

### Front Office (Public Site)
- **Home Page** - Featured articles & recent posts
- **Article View** - Full article with metadata
- **Comments** - Submit & view approved comments
- **Likes** - Like articles (tracked per session)
- **Search** - Live search by title
- **Categories** - Browse articles by category

### Back Office (Admin Panel)
- **Dashboard** - Statistics & recent activity
- **Articles** - Create, edit, delete posts
- **Moderation** - Approve/reject pending comments
- **Author Info** - Displays author details

## How It Works

### Likes
1. User clicks heart icon on article
2. AJAX sends GET request to `frontOffice.php?action=like&id={postId}`
3. PostController increments `likes_count` in database
4. Like is registered in session to prevent duplicates
5. Count updates in real-time on page

### Comments
1. User submits comment form
2. AJAX sends POST to `frontOffice.php?action=add_comment`
3. CommentController validates & inserts with `statut='en_attente'`
4. Comment awaits admin moderation
5. Once approved by admin, appears on article

### Database Constraints
- All posts must have a valid `auteur_id` from `utilisateurs` table
- All comments must have valid `id_post` and `id_utilisateur`
- Comments default to user ID 4 (no auth system)

## Default User (for Comments)
- **User ID**: 4
- **Name**: Test Reader
- **Email**: test@mediflow.com
- **Role**: User

## Troubleshooting

**Comments not saving?**
- Run `debug_db.php` to verify tables exist
- Check that user ID 4 exists in `utilisateurs` table
- Look for foreign key constraint errors in database logs

**Likes not incrementing?**
- Verify `posts` table exists with `likes_count` column
- Check browser console for JavaScript errors
- Ensure session is initialized (`session_start()`)

**No articles showing?**
- Run `init_tables.php` to populate sample data
- Verify posts have `statut='publie'`
- Check that author IDs exist in `utilisateurs` table
