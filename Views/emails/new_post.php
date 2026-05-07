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
        .article-box { background: white; padding: 20px; margin: 20px 0; border-radius: 4px; border-left: 4px solid #005851; }
        .article-box h3 { margin: 0 0 10px 0; color: #004d99; }
        .article-box p { margin: 10px 0; }
        .button { display: inline-block; padding: 12px 30px; background: #004d99; color: white; text-decoration: none; border-radius: 4px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📰 New Article Published</h1>
        </div>
        <div class="content">
            <p>Hello! We've just published a new article on MediFlow Magazine:</p>
            <div class="article-box">
                <h3><?= htmlspecialchars($postTitle) ?></h3>
                <p><?= htmlspecialchars($excerpt) ?></p>
                <a href="<?= htmlspecialchars($postUrl) ?>" class="button">Read Full Article</a>
            </div>
            <p>Stay updated with the latest health and medical insights from MediFlow Magazine!</p>
        </div>
        <div class="footer">
            <p>© 2026 MediFlow Magazine. All rights reserved.</p>
            <p><a href="<?= htmlspecialchars($_ENV['APP_URL'] ?? 'https://mediflow.local') ?>/unsubscribe" style="color: #999;">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>
