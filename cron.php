<?php
/**
 * Automated Site Monitoring Cron Job
 * Run this script via cron to automatically check all sites
 * 
 * Example cron entries:
 * Check every 5 minutes: 
 * 0,5,10,15,20,25,30,35,40,45,50,55 * * * * /usr/bin/php /path/to/your/site-monitor/cron.php
 * 
 * Check every hour:
 * 0 * * * * /usr/bin/php /path/to/your/site-monitor/cron.php
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/monitor.php';

// Set time limit for long-running checks
set_time_limit(300); // 5 minutes

// Log file for cron activities
$logFile = DATA_DIR . 'cron.log';

function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    echo $logEntry; // Also output to console
}

function sendEmailAlert($site, $type) {
    if (!EMAIL_ENABLED) {
        return false;
    }
    
    $subject = $type === 'down' 
        ? "🔴 Site Down Alert: {$site['name']}" 
        : "🟢 Site Back Online: {$site['name']}";
        
    $message = $type === 'down'
        ? "Your site \"{$site['name']}\" ({$site['url']}) is currently down and not responding.\n\nError: {$site['error_message']}\nLast checked: {$site['last_checked']}"
        : "Your site \"{$site['name']}\" ({$site['url']}) is back online and responding normally.\n\nResponse time: {$site['response_time']}ms\nStatus code: {$site['status_code']}\nLast checked: {$site['last_checked']}";
    
    // Simple mail function - you can replace this with PHPMailer or similar
    $headers = "From: " . ALERT_EMAIL . "\r\n";
    $headers .= "Reply-To: " . ALERT_EMAIL . "\r\n";
    $headers .= "X-Mailer: Site Monitor\r\n";
    
    return mail(ALERT_EMAIL, $subject, $message, $headers);
}

try {
    logMessage("Starting automated site monitoring...");
    
    $monitor = SiteMonitor::getInstance();
    $sites = $monitor->getAllSites();
    
    if (empty($sites)) {
        logMessage("No sites to monitor.");
        exit(0);
    }
    
    logMessage("Found " . count($sites) . " sites to check");
    
    $checkedCount = 0;
    $onlineCount = 0;
    $offlineCount = 0;
    $alertsSent = 0;
    
    foreach ($sites as $site) {
        if (!$site['is_active']) {
            continue;
        }
        
        logMessage("Checking site: {$site['name']} ({$site['url']})");
        
        $oldStatus = $site['status'];
        $result = $monitor->checkSite($site['id']);
        
        if ($result['success']) {
            $checkedSite = $result['site'];
            $checkedCount++;
            
            if ($checkedSite['status'] === 'online') {
                $onlineCount++;
                logMessage("✓ {$site['name']} is ONLINE - {$checkedSite['response_time']}ms - HTTP {$checkedSite['status_code']}");
                
                // Send alert if site was previously offline
                if ($oldStatus === 'offline') {
                    if (sendEmailAlert($checkedSite, 'back_online')) {
                        $alertsSent++;
                        logMessage("📧 Sent 'back online' alert for {$site['name']}");
                    }
                }
            } else {
                $offlineCount++;
                $errorMsg = $checkedSite['error_message'] ?? 'Unknown error';
                logMessage("✗ {$site['name']} is OFFLINE - Error: $errorMsg");
                
                // Send alert if site was previously online
                if ($oldStatus === 'online') {
                    if (sendEmailAlert($checkedSite, 'down')) {
                        $alertsSent++;
                        logMessage("📧 Sent 'site down' alert for {$site['name']}");
                    }
                }
            }
        } else {
            logMessage("Failed to check {$site['name']}: " . ($result['message'] ?? 'Unknown error'));
        }
        
        // Small delay between checks to avoid overwhelming servers
        usleep(500000); // 0.5 seconds
    }
    
    logMessage("Monitoring completed:");
    logMessage("- Sites checked: $checkedCount");
    logMessage("- Online: $onlineCount");
    logMessage("- Offline: $offlineCount");
    logMessage("- Alerts sent: $alertsSent");
    
    // Clean up old log entries (keep last 1000 lines)
    if (file_exists($logFile)) {
        $lines = file($logFile);
        if (count($lines) > 1000) {
            $lines = array_slice($lines, -1000);
            file_put_contents($logFile, implode('', $lines));
            logMessage("Log file cleaned up");
        }
    }
    
} catch (Exception $e) {
    logMessage("CRON ERROR: " . $e->getMessage());
    error_log("Site Monitor Cron Error: " . $e->getMessage());
    exit(1);
}

logMessage("Cron job completed successfully");
exit(0);
?>
