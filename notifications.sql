-- Notifications table schema (MediFlow)
-- Used by Models\Notification and Core\NotificationHelper
--
-- Expected columns in code:
-- - id, type, title, message, icon, color, user_id, is_read, created_at

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(100) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `icon` VARCHAR(100) NOT NULL DEFAULT 'info',
  `color` VARCHAR(50) NOT NULL DEFAULT 'primary',
  `user_id` INT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user_read_created` (`user_id`, `is_read`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

