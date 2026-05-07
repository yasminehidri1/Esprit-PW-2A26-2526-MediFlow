<?php
/**
 * Email Configuration for MediFlow
 * Uses PHPMailer with Gmail SMTP
 */

return [
    'smtp' => [
        'host'       => 'smtp.gmail.com',
        'port'       => 587,
        'username'   => 'khalil05cherif@gmail.com',
        'password'   => 'cpcf cgla tqxv pftq', // App-specific password
        'encryption' => 'tls',
        'from_email' => 'khalil05cherif@gmail.com',
        'from_name'  => 'MediFlow Magazine',
    ],
    
    'templates' => [
        'notification_like' => [
            'subject' => '❤️ Someone liked your article on MediFlow',
            'template' => 'emails/like_notification.php'
        ],
        'notification_comment' => [
            'subject' => '💬 New comment on your MediFlow article',
            'template' => 'emails/comment_notification.php'
        ],
        'new_post' => [
            'subject' => '📰 New article published on MediFlow',
            'template' => 'emails/new_post.php'
        ],
        'welcome' => [
            'subject' => 'Welcome to MediFlow Magazine',
            'template' => 'emails/welcome.php'
        ]
    ]
];
