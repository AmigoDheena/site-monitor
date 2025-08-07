<?php
require_once 'config.php';

class Auth {
    private static $instance = null;
    
    private function __construct() {
        $this->initializeDataDirectory();
        $this->initializeUsers();
        $this->startSession();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function initializeDataDirectory() {
        if (!file_exists(DATA_DIR)) {
            mkdir(DATA_DIR, 0750, true);
        }
        
        // Create .htaccess to deny web access to data directory
        $htaccess = DATA_DIR . '.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, "Deny from all\nOptions -Indexes");
        }
    }
    
    private function initializeUsers() {
        if (!file_exists(USERS_FILE)) {
            $defaultUser = [
                'users' => [
                    [
                        'id' => 1,
                        'username' => DEFAULT_ADMIN_USERNAME,
                        'password' => password_hash(DEFAULT_ADMIN_PASSWORD, PASSWORD_DEFAULT),
                        'email' => 'admin@yourdomain.com',
                        'role' => 'admin',
                        'created_at' => date('Y-m-d H:i:s'),
                        'last_login' => null,
                        'is_active' => true
                    ]
                ]
            ];
            $this->saveJsonFile(USERS_FILE, $defaultUser);
        }
    }
    
    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function login($username, $password, $rememberMe = false) {
        $clientIP = $this->getClientIP();
        
        // Check if IP is locked out
        if ($this->isIPLockedOut($clientIP)) {
            return [
                'success' => false,
                'message' => 'Too many failed login attempts. Please try again later.',
                'lockout_time' => $this->getRemainingLockoutTime($clientIP)
            ];
        }
        
        $users = $this->loadJsonFile(USERS_FILE);
        
        foreach ($users['users'] as &$user) {
            if ($user['username'] === $username && $user['is_active']) {
                if (password_verify($password, $user['password'])) {
                    // Successful login
                    $this->clearLoginAttempts($clientIP);
                    $this->createSession($user);
                    
                    // Update last login
                    $user['last_login'] = date('Y-m-d H:i:s');
                    $this->saveJsonFile(USERS_FILE, $users);
                    
                    // Handle remember me
                    if ($rememberMe) {
                        $this->setRememberMeCookie($user['id']);
                    }
                    
                    return [
                        'success' => true,
                        'message' => 'Login successful',
                        'user' => [
                            'id' => $user['id'],
                            'username' => $user['username'],
                            'role' => $user['role']
                        ]
                    ];
                }
            }
        }
        
        // Failed login
        $this->recordFailedLogin($clientIP);
        
        return [
            'success' => false,
            'message' => 'Invalid username or password',
            'attempts_remaining' => MAX_LOGIN_ATTEMPTS - $this->getLoginAttempts($clientIP)
        ];
    }
    
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            // Clear remember me cookie
            if (isset($_COOKIE['remember_token'])) {
                setcookie('remember_token', '', time() - 3600, '/', '', true, true);
            }
            
            session_destroy();
            return true;
        }
        return false;
    }
    
    public function isLoggedIn() {
        if (isset($_SESSION['user_id']) && isset($_SESSION['expires_at'])) {
            if (time() < $_SESSION['expires_at']) {
                return true;
            } else {
                // Session expired
                $this->logout();
            }
        }
        
        // Check remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            return $this->validateRememberToken($_COOKIE['remember_token']);
        }
        
        return false;
    }
    
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            $users = $this->loadJsonFile(USERS_FILE);
            foreach ($users['users'] as $user) {
                if ($user['id'] == $_SESSION['user_id']) {
                    return [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ];
                }
            }
        }
        return null;
    }
    
    public function changePassword($currentPassword, $newPassword) {
        $user = $this->getCurrentUser();
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        $users = $this->loadJsonFile(USERS_FILE);
        
        foreach ($users['users'] as &$userData) {
            if ($userData['id'] == $user['id']) {
                if (password_verify($currentPassword, $userData['password'])) {
                    $userData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                    $this->saveJsonFile(USERS_FILE, $users);
                    return ['success' => true, 'message' => 'Password changed successfully'];
                } else {
                    return ['success' => false, 'message' => 'Current password is incorrect'];
                }
            }
        }
        
        return ['success' => false, 'message' => 'Failed to change password'];
    }
    
    private function createSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();
        $_SESSION['expires_at'] = time() + SESSION_TIMEOUT;
    }
    
    private function setRememberMeCookie($userId) {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        
        // Store hashed token in user data (you might want a separate table for this)
        $users = $this->loadJsonFile(USERS_FILE);
        foreach ($users['users'] as &$user) {
            if ($user['id'] == $userId) {
                $user['remember_token'] = $hashedToken;
                $user['remember_expires'] = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 days
                break;
            }
        }
        $this->saveJsonFile(USERS_FILE, $users);
        
        // Set cookie
        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
    }
    
    private function validateRememberToken($token) {
        $hashedToken = hash('sha256', $token);
        $users = $this->loadJsonFile(USERS_FILE);
        
        foreach ($users['users'] as $user) {
            if (isset($user['remember_token']) && 
                $user['remember_token'] === $hashedToken && 
                $user['is_active'] &&
                strtotime($user['remember_expires']) > time()) {
                
                $this->createSession($user);
                return true;
            }
        }
        
        return false;
    }
    
    private function getClientIP() {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $ip;
                    }
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    private function recordFailedLogin($ip) {
        $attempts = $this->loadJsonFile(LOGIN_ATTEMPTS_FILE, ['attempts' => []]);
        
        $found = false;
        foreach ($attempts['attempts'] as &$attempt) {
            if ($attempt['ip'] === $ip) {
                $attempt['count']++;
                $attempt['last_attempt'] = time();
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $attempts['attempts'][] = [
                'ip' => $ip,
                'count' => 1,
                'last_attempt' => time()
            ];
        }
        
        $this->saveJsonFile(LOGIN_ATTEMPTS_FILE, $attempts);
    }
    
    private function getLoginAttempts($ip) {
        $attempts = $this->loadJsonFile(LOGIN_ATTEMPTS_FILE, ['attempts' => []]);
        
        foreach ($attempts['attempts'] as $attempt) {
            if ($attempt['ip'] === $ip) {
                // Reset attempts if lockout time has passed
                if (time() - $attempt['last_attempt'] > LOGIN_LOCKOUT_TIME) {
                    return 0;
                }
                return $attempt['count'];
            }
        }
        
        return 0;
    }
    
    private function isIPLockedOut($ip) {
        return $this->getLoginAttempts($ip) >= MAX_LOGIN_ATTEMPTS;
    }
    
    private function getRemainingLockoutTime($ip) {
        $attempts = $this->loadJsonFile(LOGIN_ATTEMPTS_FILE, ['attempts' => []]);
        
        foreach ($attempts['attempts'] as $attempt) {
            if ($attempt['ip'] === $ip) {
                $timeRemaining = LOGIN_LOCKOUT_TIME - (time() - $attempt['last_attempt']);
                return max(0, $timeRemaining);
            }
        }
        
        return 0;
    }
    
    private function clearLoginAttempts($ip) {
        $attempts = $this->loadJsonFile(LOGIN_ATTEMPTS_FILE, ['attempts' => []]);
        
        $attempts['attempts'] = array_filter($attempts['attempts'], function($attempt) use ($ip) {
            return $attempt['ip'] !== $ip;
        });
        
        $this->saveJsonFile(LOGIN_ATTEMPTS_FILE, $attempts);
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
