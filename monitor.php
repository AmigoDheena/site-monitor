<?php
require_once 'config.php';

class SiteMonitor {
    private static $instance = null;
    
    private function __construct() {
        $this->initializeSitesFile();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function initializeSitesFile() {
        // Create data directory if it doesn't exist
        if (!file_exists(DATA_DIR)) {
            mkdir(DATA_DIR, 0750, true);
            
            // Create .htaccess to deny web access to data directory
            $htaccess = DATA_DIR . '.htaccess';
            file_put_contents($htaccess, "Deny from all\nOptions -Indexes");
        }
        
        if (!file_exists(SITES_FILE)) {
            $defaultData = [
                'sites' => [],
                'last_updated' => date('Y-m-d H:i:s')
            ];
            $this->saveJsonFile(SITES_FILE, $defaultData);
        }
    }
    
    public function addSite($name, $url, $checkInterval = 300) {
        $sites = $this->loadJsonFile(SITES_FILE);
        
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'message' => 'Invalid URL format'];
        }
        
        // Check if site already exists
        foreach ($sites['sites'] as $site) {
            if ($site['url'] === $url) {
                return ['success' => false, 'message' => 'Site already exists'];
            }
        }
        
        $newSite = [
            'id' => $this->generateUniqueId(),
            'name' => trim($name),
            'url' => trim($url),
            'status' => 'pending',
            'status_code' => 0,
            'response_time' => 0,
            'last_checked' => null,
            'last_online' => null,
            'last_offline' => null,
            'check_interval' => max(60, $checkInterval), // Minimum 1 minute
            'is_active' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'uptime_percentage' => 100,
            'total_checks' => 0,
            'successful_checks' => 0,
            'failed_checks' => 0,
            'ssl_info' => null,
            'domain_expires' => null,
            'error_message' => null
        ];
        
        $sites['sites'][] = $newSite;
        $sites['last_updated'] = date('Y-m-d H:i:s');
        
        if ($this->saveJsonFile(SITES_FILE, $sites)) {
            return [
                'success' => true, 
                'message' => 'Site added successfully',
                'site' => $newSite
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to add site'];
    }
    
    public function updateSite($id, $name, $url, $checkInterval = null) {
        $sites = $this->loadJsonFile(SITES_FILE);
        
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'message' => 'Invalid URL format'];
        }
        
        foreach ($sites['sites'] as &$site) {
            if ($site['id'] == $id) {
                // Check if new URL conflicts with existing sites
                foreach ($sites['sites'] as $existingSite) {
                    if ($existingSite['id'] != $id && $existingSite['url'] === $url) {
                        return ['success' => false, 'message' => 'URL already exists for another site'];
                    }
                }
                
                $site['name'] = trim($name);
                $site['url'] = trim($url);
                if ($checkInterval !== null) {
                    $site['check_interval'] = max(60, $checkInterval);
                }
                
                $sites['last_updated'] = date('Y-m-d H:i:s');
                
                if ($this->saveJsonFile(SITES_FILE, $sites)) {
                    return [
                        'success' => true, 
                        'message' => 'Site updated successfully',
                        'site' => $site
                    ];
                }
                break;
            }
        }
        
        return ['success' => false, 'message' => 'Site not found or update failed'];
    }
    
    public function deleteSite($id) {
        $sites = $this->loadJsonFile(SITES_FILE);
        
        $initialCount = count($sites['sites']);
        $sites['sites'] = array_filter($sites['sites'], function($site) use ($id) {
            return $site['id'] != $id;
        });
        
        if (count($sites['sites']) < $initialCount) {
            $sites['last_updated'] = date('Y-m-d H:i:s');
            
            if ($this->saveJsonFile(SITES_FILE, $sites)) {
                return ['success' => true, 'message' => 'Site deleted successfully'];
            }
        }
        
        return ['success' => false, 'message' => 'Site not found or delete failed'];
    }
    
    public function getAllSites() {
        $sites = $this->loadJsonFile(SITES_FILE);
        return $sites['sites'] ?? [];
    }
    
    public function getSite($id) {
        $sites = $this->getAllSites();
        foreach ($sites as $site) {
            if ($site['id'] == $id) {
                return $site;
            }
        }
        return null;
    }
    
    public function checkSite($id) {
        $sites = $this->loadJsonFile(SITES_FILE);
        
        foreach ($sites['sites'] as &$site) {
            if ($site['id'] == $id) {
                $result = $this->performSiteCheck($site['url']);
                
                // Update site data
                $site['status'] = $result['status'];
                $site['status_code'] = $result['status_code'];
                $site['response_time'] = $result['response_time'];
                $site['last_checked'] = date('Y-m-d H:i:s');
                $site['error_message'] = $result['error_message'];
                $site['ssl_info'] = $result['ssl_info'];
                $site['domain_expires'] = $result['domain_expires'];
                $site['total_checks']++;
                
                if ($result['status'] === 'online') {
                    $site['last_online'] = date('Y-m-d H:i:s');
                    $site['successful_checks']++;
                } else {
                    $site['last_offline'] = date('Y-m-d H:i:s');
                    $site['failed_checks']++;
                }
                
                // Calculate uptime percentage
                if ($site['total_checks'] > 0) {
                    $site['uptime_percentage'] = round(
                        ($site['successful_checks'] / $site['total_checks']) * 100, 
                        2
                    );
                }
                
                $sites['last_updated'] = date('Y-m-d H:i:s');
                $this->saveJsonFile(SITES_FILE, $sites);
                
                return [
                    'success' => true,
                    'site' => $site,
                    'check_result' => $result
                ];
            }
        }
        
        return ['success' => false, 'message' => 'Site not found'];
    }
    
    public function checkAllSites() {
        $sites = $this->getAllSites();
        $results = [];
        
        foreach ($sites as $site) {
            if ($site['is_active']) {
                $result = $this->checkSite($site['id']);
                $results[] = $result;
                
                // Small delay to avoid overwhelming the server
                usleep(500000); // 0.5 seconds
            }
        }
        
        return $results;
    }
    
    private function performSiteCheck($url) {
        $result = [
            'status' => 'offline',
            'status_code' => 0,
            'response_time' => 0,
            'error_message' => null,
            'ssl_info' => null,
            'domain_expires' => null
        ];
        
        $startTime = microtime(true);
        
        // Initialize cURL
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => MAX_REDIRECTS,
            CURLOPT_TIMEOUT => DEFAULT_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_USERAGENT => USER_AGENT,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_NOBODY => true, // HEAD request
            CURLOPT_HEADER => true,
            CURLOPT_CERTINFO => true
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        // Get SSL certificate info
        $certInfo = curl_getinfo($ch, CURLINFO_CERTINFO);
        
        $endTime = microtime(true);
        $result['response_time'] = round(($endTime - $startTime) * 1000, 2); // Convert to milliseconds
        
        if ($error) {
            $result['error_message'] = $error;
        } else {
            $result['status_code'] = $httpCode;
            
            // Consider 2xx and 3xx as online
            if ($httpCode >= 200 && $httpCode < 400) {
                $result['status'] = 'online';
            } elseif ($httpCode >= 400 && $httpCode < 500) {
                $result['status'] = 'warning'; // Client error
                $result['error_message'] = "HTTP $httpCode - Client Error";
            } else {
                $result['status'] = 'offline';
                $result['error_message'] = "HTTP $httpCode - Server Error";
            }
        }
        
        // Get SSL information
        if (!empty($certInfo) && isset($certInfo[0])) {
            $cert = $certInfo[0];
            $result['ssl_info'] = [
                'valid_from' => $cert['Start date'] ?? null,
                'valid_to' => $cert['Expire date'] ?? null,
                'issuer' => $cert['Issuer'] ?? null,
                'subject' => $cert['Subject'] ?? null
            ];
        }
        
        curl_close($ch);
        
        // Check domain expiration (simplified - you might want to use WHOIS for more accuracy)
        $result['domain_expires'] = $this->getDomainExpiration($url);
        
        return $result;
    }
    
    private function getDomainExpiration($url) {
        // Extract domain from URL
        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['host'] ?? '';
        
        // Remove www. prefix
        $domain = preg_replace('/^www\./', '', $domain);
        
        // This is a simplified check - for production, you'd want to use a proper WHOIS library
        // For now, we'll return null and you can integrate a WHOIS service later
        return null;
    }
    
    public function getStats() {
        $sites = $this->getAllSites();
        $stats = [
            'total_sites' => count($sites),
            'online_sites' => 0,
            'offline_sites' => 0,
            'warning_sites' => 0,
            'pending_sites' => 0,
            'average_response_time' => 0,
            'overall_uptime' => 0
        ];
        
        $totalResponseTime = 0;
        $responsiveSites = 0;
        $totalUptime = 0;
        $sitesWithData = 0;
        
        foreach ($sites as $site) {
            switch ($site['status']) {
                case 'online':
                    $stats['online_sites']++;
                    break;
                case 'offline':
                    $stats['offline_sites']++;
                    break;
                case 'warning':
                    $stats['warning_sites']++;
                    break;
                default:
                    $stats['pending_sites']++;
            }
            
            if ($site['response_time'] > 0) {
                $totalResponseTime += $site['response_time'];
                $responsiveSites++;
            }
            
            if ($site['total_checks'] > 0) {
                $totalUptime += $site['uptime_percentage'];
                $sitesWithData++;
            }
        }
        
        if ($responsiveSites > 0) {
            $stats['average_response_time'] = round($totalResponseTime / $responsiveSites, 2);
        }
        
        if ($sitesWithData > 0) {
            $stats['overall_uptime'] = round($totalUptime / $sitesWithData, 2);
        }
        
        return $stats;
    }
    
    private function generateUniqueId() {
        return time() . rand(1000, 9999);
    }
    
    private function loadJsonFile($filename, $default = []) {
        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            $data = json_decode($content, true);
            return $data !== null ? $data : $default;
        }
        return $default;
    }
    
    private function saveJsonFile($filename, $data) {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($filename, $json, LOCK_EX) !== false;
    }
}
?>
