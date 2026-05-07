<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - MediFlow</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Inter:wght@100..900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-6xl mx-auto px-6 py-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
                <p class="text-sm text-gray-600 mt-1">You have <?= $unreadCount ?> unread notification<?= $unreadCount !== 1 ? 's' : '' ?></p>
            </div>
            
            <?php if ($unreadCount > 0): ?>
            <button onclick="markAllRead()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined">done_all</span>
                Mark All as Read
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="max-w-6xl mx-auto px-6 py-8">
        <?php if (empty($notifications)): ?>
            <div class="text-center py-16">
                <span class="material-symbols-outlined text-6xl text-gray-300 block mb-4">notifications_off</span>
                <p class="text-gray-500 text-lg">No notifications yet</p>
                <p class="text-gray-400 text-sm mt-2">You'll see notifications here when there's activity on your posts and comments</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($notifications as $notif): 
                    $bgClass = $notif['is_read'] ? 'bg-gray-50' : 'bg-blue-50';
                    $borderClass = $notif['is_read'] ? 'border-gray-200' : 'border-blue-200 border-l-4 border-l-blue-500';
                    $typeEmoji = match($notif['type']) {
                        'new_comment' => '💬',
                        'post_liked' => '❤️',
                        'comment_on_your_post' => '💬',
                        'order_status' => '📦',
                        'appointment_confirmed' => '📅',
                        'appointment_reminder' => '🔔',
                        'equipment_reserved' => '✅',
                        'prescription_ready' => '💊',
                        'new_prescription' => '📋',
                        default => 'ℹ️'
                    };
                ?>
                    <div class="<?= $bgClass ?> <?= $borderClass ?> border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer" onclick="handleNotificationClick(<?= $notif['id'] ?>)">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-12 h-12 rounded-full" style="background-color: <?= $notif['color'] === 'error' ? 'rgba(239, 68, 68, 0.1)' : ($notif['color'] === 'tertiary' ? 'rgba(0, 88, 81, 0.1)' : 'rgba(0, 77, 153, 0.1)') ?>">
                                    <span class="material-symbols-outlined" style="color: <?= $notif['color'] === 'error' ? '#ef4444' : ($notif['color'] === 'tertiary' ? '#005851' : '#004d99') ?>">
                                        <?= htmlspecialchars($notif['icon']) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-grow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($notif['title']) ?></h3>
                                        <p class="text-gray-600 text-sm mt-1"><?= htmlspecialchars($notif['message']) ?></p>
                                    </div>
                                    <?php if (!$notif['is_read']): ?>
                                    <span class="ml-4 flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        New
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                                    <span><?= Notification::getTimeAgo($notif['created_at']) ?></span>
                                    <?php if (!$notif['is_read']): ?>
                                    <button onclick="markAsRead(event, <?= $notif['id'] ?>)" class="text-blue-600 hover:text-blue-700 font-medium">Mark as read</button>
                                    <?php endif; ?>
                                    <button onclick="deleteNotification(event, <?= $notif['id'] ?>)" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="mt-8 flex justify-center gap-2">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <?php if ($p === $page): ?>
                    <span class="px-4 py-2 rounded-lg bg-blue-600 text-white font-medium"><?= $p ?></span>
                    <?php else: ?>
                    <a href="?page=<?= $p ?>" class="px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors"><?= $p ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        async function markAsRead(e, notificationId) {
            e.stopPropagation();
            const response = await fetch('/integration/notifications/mark-read?id=' + notificationId, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (response.ok) {
                location.reload();
            }
        }

        async function deleteNotification(e, notificationId) {
            e.stopPropagation();
            if (confirm('Delete this notification?')) {
                const response = await fetch('/integration/notifications/delete?id=' + notificationId, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (response.ok) {
                    location.reload();
                }
            }
        }

        async function markAllRead() {
            const response = await fetch('/integration/notifications/mark-all-read', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (response.ok) {
                location.reload();
            }
        }

        function handleNotificationClick(notificationId) {
            // Mark as read on click
            fetch('/integration/notifications/mark-read?id=' + notificationId, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(() => {
                location.reload();
            });
        }
    </script>
</body>
</html>
