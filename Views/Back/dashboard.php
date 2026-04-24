<?php
/**
 * Back Office — User Dashboard Entry Point
 *
 * This file is the entry point included by DashboardController::index().
 * It routes through the unified back-office layout shell (layout.php).
 * The actual KPI content is in dashboard_kpi.php (rendered by layout.php).
 */

// $data is set by DashboardController::index() before this file is included.
// layout.php checks for $currentView; when unset it falls back to dashboard_kpi.php.
// We don't set $currentView here so layout.php's else-branch picks up dashboard_kpi.php.

include __DIR__ . '/layout.php';
