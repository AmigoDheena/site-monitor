<?php
// Site Monitor Configuration
define('APP_NAME', 'Site Status Monitor');
define('APP_VERSION', '1.0.0');

// Security Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes in seconds

// File Paths (outside web root for security)
define('DATA_DIR', __DIR__ . '/data/');
define('USERS_FILE', DATA_DIR . 'users.json');
define('SITES_FILE', DATA_DIR . 'sites.json');
define('LOGIN_ATTEMPTS_FILE', DATA_DIR . 'login_attempts.json');

// Email Configuration (optional)
define('EMAIL_ENABLED', false);
define('SMTP_HOST', 'your-smtp-host.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@domain.com');
define('SMTP_PASSWORD', 'your-email-password');
define('ALERT_EMAIL', 'admin@yourdomain.com');

// Site Check Configuration
define('DEFAULT_TIMEOUT', 30); // seconds
define('USER_AGENT', 'Site Monitor Bot/1.0');
define('MAX_REDIRECTS', 5);

// Default admin user (change these credentials!)
define('DEFAULT_ADMIN_USERNAME', 'admin');
define('DEFAULT_ADMIN_PASSWORD', 'SiteMonitor@2025!'); // Change this!

// Timezone
date_default_timezone_set('Asia/Dhaka'); // Change to your timezone

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>