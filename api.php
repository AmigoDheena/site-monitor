<?php
require_once 'config.php';
require_once 'monitor.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$monitor = SiteMonitor::getInstance();

$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['action'] ?? '';

// Helper function to get request body
function getRequestBody() {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

// Helper function to send JSON response
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

// Helper function to validate required fields
function validateFields($data, $required) {
    $missing = [];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $missing[] = $field;
        }
    }
    return $missing;
}

try {
    switch ($path) {
        case 'sites':
            switch ($method) {
                case 'GET':
                    $sites = $monitor->getAllSites();
                    sendResponse([
                        'success' => true,
                        'sites' => $sites,
                        'stats' => $monitor->getStats()
                    ]);
                    break;
                    
                case 'POST':
                    $data = getRequestBody();
                    $missing = validateFields($data, ['name', 'url']);
                    
                    if (!empty($missing)) {
                        sendResponse([
                            'success' => false,
                            'message' => 'Missing required fields: ' . implode(', ', $missing)
                        ], 400);
                    }
                    
                    $checkInterval = $data['check_interval'] ?? 300;
                    $result = $monitor->addSite($data['name'], $data['url'], $checkInterval);
                    
                    sendResponse($result, $result['success'] ? 201 : 400);
                    break;
                    
                default:
                    sendResponse(['error' => 'Method not allowed'], 405);
            }
            break;
            
        case 'site':
            $siteId = $_GET['id'] ?? null;
            if (!$siteId) {
                sendResponse(['error' => 'Site ID required'], 400);
            }
            
            switch ($method) {
                case 'GET':
                    $site = $monitor->getSite($siteId);
                    if (!$site) {
                        sendResponse(['error' => 'Site not found'], 404);
                    }
                    
                    sendResponse([
                        'success' => true,
                        'site' => $site
                    ]);
                    break;
                    
                case 'PUT':
                    $data = getRequestBody();
                    $missing = validateFields($data, ['name', 'url']);
                    
                    if (!empty($missing)) {
                        sendResponse([
                            'success' => false,
                            'message' => 'Missing required fields: ' . implode(', ', $missing)
                        ], 400);
                    }
                    
                    $checkInterval = $data['check_interval'] ?? null;
                    $result = $monitor->updateSite($siteId, $data['name'], $data['url'], $checkInterval);
                    
                    sendResponse($result, $result['success'] ? 200 : 400);
                    break;
                    
                case 'DELETE':
                    $result = $monitor->deleteSite($siteId);
                    sendResponse($result, $result['success'] ? 200 : 400);
                    break;
                    
                default:
                    sendResponse(['error' => 'Method not allowed'], 405);
            }
            break;
            
        case 'check-site':
            if ($method !== 'POST') {
                sendResponse(['error' => 'Method not allowed'], 405);
            }
            
            $siteId = $_GET['id'] ?? null;
            if (!$siteId) {
                sendResponse(['error' => 'Site ID required'], 400);
            }
            
            $result = $monitor->checkSite($siteId);
            sendResponse($result, $result['success'] ? 200 : 400);
            break;
            
        case 'check-all':
            if ($method !== 'POST') {
                sendResponse(['error' => 'Method not allowed'], 405);
            }
            
            // This might take a while, so increase time limit
            set_time_limit(300); // 5 minutes
            
            $results = $monitor->checkAllSites();
            sendResponse([
                'success' => true,
                'results' => $results,
                'stats' => $monitor->getStats()
            ]);
            break;
            
        case 'stats':
            if ($method !== 'GET') {
                sendResponse(['error' => 'Method not allowed'], 405);
            }
            
            $stats = $monitor->getStats();
            sendResponse([
                'success' => true,
                'stats' => $stats
            ]);
            break;
            
        default:
            sendResponse(['error' => 'Endpoint not found'], 404);
    }
    
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    sendResponse([
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ], 500);
}
?>
