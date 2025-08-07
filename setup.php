<?php
/**
 * Professional Site Monitor Setup Script
 * Initialize the application with a clean, compact interface
 */

// Set content type for proper display
header('Content-Type: text/html; charset=UTF-8');

$isWebRequest = isset($_SERVER['HTTP_HOST']);

if ($isWebRequest) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Site Monitor - Setup</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="assets/css/setup.css">
    </head>
    <body>
        <div class="setup-container">
            <!-- Hero Section -->
            <div class="hero-section">
                <div class="hero-title">
                    <i class="fas fa-shield-alt"></i> Site Monitor Setup
                </div>
                <div class="hero-subtitle">
                    Professional Website Monitoring Solution
                </div>
                
                <!-- Progress Steps -->
                <div class="progress-steps">
                    <div class="progress-step">
                        <i class="fas fa-check"></i> System Check
                    </div>
                    <div class="progress-step">
                        <i class="fas fa-check"></i> Files Created
                    </div>
                    <div class="progress-step">
                        <i class="fas fa-check"></i> Database Setup
                    </div>
                    <div class="progress-step">
                        <i class="fas fa-check"></i> Ready to Use
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="setup-card">
                <div class="setup-card-header">
                    <i class="fas fa-cogs"></i> System Initialization
                </div>
                <div class="setup-card-content"><?php
}

require_once 'config.php';

// Compact output functions
function outputMessage($message, $type = 'info', $isWebRequest = false) {
    if ($isWebRequest) {
        $icons = ['success' => 'fa-check-circle', 'error' => 'fa-times-circle', 'warning' => 'fa-exclamation-triangle', 'info' => 'fa-info-circle'];
        echo "<div class='status-message status-{$type}'><i class='fas {$icons[$type]}'></i><span>{$message}</span></div>";
    } else {
        $prefix = $type === 'success' ? '✓' : ($type === 'error' ? '✗' : '⚠');
        echo "{$prefix} {$message}\n";
    }
}

function startNewSection($title, $icon, $isWebRequest) {
    if ($isWebRequest) {
        echo "</div></div><div class='setup-card'><div class='setup-card-header'><i class='fas {$icon}'></i> {$title}</div><div class='setup-card-content'>";
    } else {
        echo "\n{$title}:\n" . str_repeat('-', strlen($title)) . "\n";
    }
}

// Initialize system
if (!$isWebRequest) {
    echo "Site Monitor Setup\n";
    echo "==================\n\n";
}

// Create data directory
if (!file_exists(DATA_DIR)) {
    if (mkdir(DATA_DIR, 0750, true)) {
        outputMessage("Created data directory: " . DATA_DIR, 'success', $isWebRequest);
    } else {
        outputMessage("Failed to create data directory: " . DATA_DIR, 'error', $isWebRequest);
        exit(1);
    }
} else {
    outputMessage("Data directory already exists", 'success', $isWebRequest);
}

// Set permissions and security
if (is_writable(DATA_DIR)) {
    outputMessage("Data directory is writable", 'success', $isWebRequest);
} else {
    outputMessage("Data directory needs write permissions", 'error', $isWebRequest);
}

// Create .htaccess
$dataHtaccess = DATA_DIR . '.htaccess';
if (!file_exists($dataHtaccess)) {
    $htaccessContent = "Order Deny,Allow\nDeny from all\nOptions -Indexes\n";
    if (file_put_contents($dataHtaccess, $htaccessContent)) {
        outputMessage("Created security protection", 'success', $isWebRequest);
    }
} else {
    outputMessage("Security protection exists", 'success', $isWebRequest);
}

// Initialize systems
try {
    require_once 'auth.php';
    $auth = Auth::getInstance();
    outputMessage("Authentication system ready", 'success', $isWebRequest);
} catch (Exception $e) {
    outputMessage("Authentication error: " . $e->getMessage(), 'error', $isWebRequest);
}

try {
    require_once 'monitor.php';
    $monitor = SiteMonitor::getInstance();
    outputMessage("Monitoring system ready", 'success', $isWebRequest);
} catch (Exception $e) {
    outputMessage("Monitoring error: " . $e->getMessage(), 'error', $isWebRequest);
}

// Check files
startNewSection('File Verification', 'fa-file-check', $isWebRequest);
$files = ['index.php', 'api.php', 'config.php', 'auth.php', 'monitor.php', 'cron.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        outputMessage("{$file} found", 'success', $isWebRequest);
    } else {
        outputMessage("{$file} missing", 'error', $isWebRequest);
    }
}

// Check PHP extensions
startNewSection('PHP Extensions', 'fa-code', $isWebRequest);
$extensions = ['curl' => 'Site checking', 'json' => 'Data storage', 'openssl' => 'SSL/HTTPS'];
foreach ($extensions as $ext => $purpose) {
    if (extension_loaded($ext)) {
        outputMessage("{$ext} extension available", 'success', $isWebRequest);
    } else {
        outputMessage("{$ext} extension missing - {$purpose}", 'error', $isWebRequest);
    }
}

// Show credentials if web request
if ($isWebRequest && file_exists(USERS_FILE)) {
    $users = json_decode(file_get_contents(USERS_FILE), true);
    if (!empty($users['users'])) {
        startNewSection('Login Credentials', 'fa-key', $isWebRequest);
        echo "<div class='credentials-box'>";
        echo "<h4 class='mb-1'><i class='fas fa-user-shield'></i> Default Admin Access</h4>";
        echo "<div class='credentials-item'>";
        echo "<span><strong>Username:</strong></span>";
        echo "<span class='credentials-code'>" . DEFAULT_ADMIN_USERNAME . "</span>";
        echo "</div>";
        echo "<div class='credentials-item'>";
        echo "<span><strong>Password:</strong></span>";
        echo "<span class='credentials-code'>" . DEFAULT_ADMIN_PASSWORD . "</span>";
        echo "</div>";
        echo "<div class='security-alert'>";
        echo "<i class='fas fa-exclamation-triangle'></i> <strong>Important:</strong> Change the default password immediately after first login!";
        echo "</div>";
        echo "</div>";
    }
}

if ($isWebRequest) {
    ?>
                </div>
            </div>

            <!-- Quick Start Guide -->
            <div class="setup-card">
                <div class="setup-card-header">
                    <i class="fas fa-rocket"></i> Quick Start Guide
                </div>
                <div class="setup-card-content">
                    <ol class="quick-steps">
                        <li class="quick-step">
                            <div>
                                <strong>Access Dashboard</strong><br>
                                <a href="index.php" style="color: var(--primary-color);">Click here to open your monitoring dashboard</a>
                            </div>
                        </li>
                        <li class="quick-step">
                            <div>
                                <strong>Login</strong><br>
                                Use the default credentials shown above
                            </div>
                        </li>
                        <li class="quick-step">
                            <div>
                                <strong>Change Password</strong><br>
                                Update your password for security
                            </div>
                        </li>
                        <li class="quick-step">
                            <div>
                                <strong>Add Sites</strong><br>
                                Start monitoring your websites
                            </div>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Security & Configuration -->
            <div class="setup-card">
                <div class="setup-card-header">
                    <i class="fas fa-shield-alt"></i> Security & Configuration
                </div>
                <div class="setup-card-content">
                    <div class="security-grid">
                        <div class="security-item">
                            <h6><i class="fas fa-key"></i> Password Security</h6>
                            <p>Change <code>DEFAULT_ADMIN_PASSWORD</code> in config.php</p>
                        </div>
                        <div class="security-item">
                            <h6><i class="fas fa-folder-lock"></i> Data Protection</h6>
                            <p>Move data/ directory outside web root if possible</p>
                        </div>
                        <div class="security-item">
                            <h6><i class="fas fa-certificate"></i> SSL/HTTPS</h6>
                            <p>Use HTTPS in production environments</p>
                        </div>
                        <div class="security-item">
                            <h6><i class="fas fa-robot"></i> Automation</h6>
                            <p>Set up cron job for continuous monitoring</p>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <h6 class="fw-bold">Cron Job Setup (Linux/Unix):</h6>
                        <div class="code-block">*/5 * * * * /usr/bin/php <?php echo __DIR__; ?>/cron.php >> <?php echo DATA_DIR; ?>cron.log 2>&1</div>
                        <p class="text-muted"><i class="fab fa-windows"></i> <strong>Windows:</strong> Use Task Scheduler or WinCron for automation</p>
                    </div>
                </div>
            </div>

            <!-- Success Section -->
            <div class="success-section">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="success-title">
                    Setup Completed Successfully!
                </div>
                <div class="success-subtitle">
                    Your professional site monitor is ready to protect your websites
                </div>
                <div>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt"></i> Open Dashboard
                    </a>
                    <button onclick="window.location.reload()" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Run Setup Again
                    </button>
                </div>
            </div>

        </div>
    </body>
    </html>
    <?php
} else {
    // CLI output
    echo "\nSetup completed successfully!\n";
    echo "Access your monitor at: http://yourdomain.com/path-to-monitor/\n";
    echo "Username: " . DEFAULT_ADMIN_USERNAME . "\n";
    echo "Password: " . DEFAULT_ADMIN_PASSWORD . "\n\n";
}
?>