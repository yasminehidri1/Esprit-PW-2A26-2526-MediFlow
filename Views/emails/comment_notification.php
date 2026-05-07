<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #004d99 0%, #005851 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { background: #f5f5f5; padding: 30px; border-radius: 0 0 8px 8px; }
        .message { background: white; padding: 20px; border-left: 4px solid #0066cc; margin: 20px 0; border-radius: 4px; }
        .button { display: inline-block; padding: 12px 30px; background: #004d99; color: white; text-decoration: none; border-radius: 4px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💬 New Comment on Your Article</h1>
        </div>
        <div class="content">
            <p>Hi there!</p>
            <div class="message">
                <strong><?= htmlspecialchars($userName) ?></strong> just commented on your article:
                <br><br>
                <strong>"<?= htmlspecialchars($postTitle) ?>"</strong>
            </div>
            <p>Your article is generating great engagement on MediFlow Magazine!</p>
            <div style="text-align: center;">
                <a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? 'https://mediflow.local') ?>" class="button">Read Comment</a>
            </div>
        </div>
        <div class="footer">
            <p>© 2026 MediFlow Magazine. All rights reserved.</p>
            <p><a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? 'https://mediflow.local') ?>/notification-settings" style="color: #999;">Manage Notifications</a></p>
        </div>
    </div>
</body>
</html>
