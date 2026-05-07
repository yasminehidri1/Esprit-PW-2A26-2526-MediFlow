-- Email Subscribers Table for MediFlow Magazine
-- Stores email addresses of users who subscribe to new article notifications

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
    KEY status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email Log Table to track sent emails
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
