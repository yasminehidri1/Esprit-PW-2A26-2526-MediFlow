# Email Notification System Setup Guide

## What's Been Implemented ✅

### 1. **Email Service with PHPMailer**
- **File:** `Core/MailService.php`
- **SMTP Provider:** Gmail (khalil05cherif@gmail.com)
- **Status:** Ready to send emails

### 2. **Email Templates**
Located in `Views/emails/`:
- `like_notification.php` - Sent when someone likes a post
- `comment_notification.php` - Sent when someone comments
- `new_post.php` - Sent to subscribers for new articles

### 3. **Notification Helper Integration**
- `Core/NotificationHelper.php` now includes:
  - `sendLikeNotificationEmail()` - Sends email to post author
  - `sendCommentNotificationEmail()` - Sends email to post author
  - `sendNewPostNotificationToSubscribers()` - Broadcasts to all subscribers

### 4. **Email Subscriber Model**
- **File:** `Models/EmailSubscriber.php`
- **Features:**
  - Subscribe/unsubscribe users
  - Track subscriber count
  - Log sent emails
  - Check subscription status

### 5. **Subscription Controller**
- **File:** `Controllers/SubscriptionController.php`
- **Endpoints:**
  - `/subscription/subscribe` - AJAX subscription
  - `/subscription/unsubscribe` - Unsubscribe link
  - `/subscription/count` - Get subscriber count

### 6. **Updated Controllers**
- **PostController:** Sends email when post is liked
- **CommentController:** Sends email when post is commented
- Both include try-catch to prevent errors from breaking functionality

### 7. **Updated Newsletter Box**
- **File:** `Views/Front/_newsletter_box.php`
- Changed from form submission to AJAX
- Shows real-time feedback to users
- Uses new `/subscription/subscribe` endpoint

## ⚠️ CRITICAL: Run Database Migration

**Before testing, run this SQL in your database:**

```sql
-- Email Subscribers Table
CREATE TABLE IF NOT EXISTS email_subscribers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    user_id INT NULL,
    status ENUM('active', 'unsubscribed', 'bounced') DEFAULT 'active',
    subscription_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verified BOOLEAN DEFAULT 0,
    verification_token VARCHAR(255) NULL,
    unsubscribe_token VARCHAR(255) NULL UNIQUE,
    last_email_sent TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    KEY user_id (user_id),
    KEY status (status),
    KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email Log Table
CREATE TABLE IF NOT EXISTS email_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recipient_email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    email_type VARCHAR(100) NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('sent', 'failed', 'bounced') DEFAULT 'sent',
    error_message TEXT NULL,
    
    KEY recipient_email (recipient_email),
    KEY email_type (email_type),
    KEY sent_at (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**OR** copy the contents of `email_subscribers.sql` to your database.

## 🧪 Testing the Email System

### Test 1: Post Author Gets Email When Liked
1. Log in as User A
2. Create an article  
3. Log in as User B (different browser/tab)
4. Like User A's article
5. ✅ User A should receive an email: "❤️ [User B] liked your article"

### Test 2: Post Author Gets Email When Commented
1. Follow same login as Test 1
2. User B comments on User A's article
3. ✅ User A should receive an email: "💬 [User B] commented on your article"

### Test 3: Newsletter Subscription
1. Scroll to bottom of any article on front end
2. Enter email in "Medical Excellence in your inbox" box
3. Click Subscribe
4. ✅ Toast notification confirms subscription
5. Check `email_subscribers` table - email should be there

## 📧 Email Configuration

**File:** `config/email.php`

Current settings:
- Host: smtp.gmail.com
- Port: 587
- Username: khalil05cherif@gmail.com
- Password: cpcf cgla tqxv pftq (App-specific password)
- Encryption: TLS

⚠️ Keep this file secure - don't commit to version control!

## 🚀 Next Steps

### Optional: Send Emails When New Posts Are Published

Add to `PostController::create()`:
```php
// After post is successfully created
NotificationHelper::sendNewPostNotificationToSubscribers(
    $post['titre'],
    substr($post['contenu'], 0, 150) . '...',
    $postId
);
```

### Optional: Verify Email Addresses
Add email verification flow with tokens from `verification_token` field

### Optional: Email Preferences
Let users choose which notifications to receive (likes only, comments only, etc.)

## ⚡ Performance Tips

1. **Send emails asynchronously** - Large subscriber lists may take time
2. **Use a job queue** - Implement Laravel Queue or similar for bulk sends
3. **Monitor email logs** - Check `email_logs` table for failed sends
4. **Bounce handling** - Update status to 'bounced' for invalid addresses

## 🐛 Troubleshooting

### Emails not sending?
1. Check `php_errors.log` in XAMPP
2. Verify Gmail app password is correct
3. Check `email_logs` table for error messages
4. Ensure `allow_url_fopen` is enabled in php.ini

### "Not valid JSON" errors?
- Usually means email sending code threw an exception
- Check NotificationHelper try-catch blocks
- Look for "Email notification error" in logs

### Gmail authentication fails?
- Verify 2FA is enabled on Gmail account
- Regenerate app-specific password
- Update `config/email.php`

## 📝 Files Created/Modified

**New Files:**
- `Core/MailService.php`
- `Core/NotificationHelper.php` (updated)
- `Models/EmailSubscriber.php`
- `Controllers/SubscriptionController.php`
- `config/email.php`
- `Views/emails/like_notification.php`
- `Views/emails/comment_notification.php`
- `Views/emails/new_post.php`
- `email_subscribers.sql` (migration)
- `email_notification_setup.md` (this file)

**Modified Files:**
- `Controllers/PostController.php` - Added email on like
- `Controllers/CommentController.php` - Added email on comment
- `Core/App.php` - Added subscription routes
- `Core/NotificationHelper.php` - Added email methods
- `Views/Front/_newsletter_box.php` - Updated to AJAX

---

**System Ready!** 🎉
All email infrastructure is in place. Just run the database migration and you're good to go.
