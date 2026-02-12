<?php
// Application Configuration
define('APP_NAME', 'University of Vavuniya Course Registration System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/COURSE_SYSTEM');
define('UNIVERSITY_NAME', 'University of Vavuniya');
define('UNIVERSITY_EMAIL', 'info@vau.ac.lk');
define('UNIVERSITY_PHONE', '+94 24 222 2265');
define('UNIVERSITY_ADDRESS', 'Pambaimadhu, Vavuniya, Sri Lanka');

// Academic Configuration
define('CURRENT_ACADEMIC_YEAR', '2024/2025');
define('CURRENT_SEMESTER', 1);
define('MAX_COURSES_PER_STUDENT', 6);
define('MIN_PASSWORD_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes in seconds

// File Upload Configuration
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_FILE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']);
define('UPLOAD_DIR', 'uploads/');

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.gc_maxlifetime', 1800); // 30 minutes

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'noreply@vau.ac.lk');
define('SMTP_PASS', '');
define('SMTP_SECURE', 'tls');

// Debug Mode
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Asia/Colombo');

// Create necessary directories
function create_directories() {
    $directories = [
        'uploads',
        'uploads/students',
        'uploads/courses',
        'logs',
        'backups'
    ];
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

create_directories();

// Log function
function log_message($message, $level = 'INFO') {
    $log_file = 'logs/system_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message" . PHP_EOL;
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Generate CSRF token
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Generate random string
function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $random_string = '';
    
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, $characters_length - 1)];
    }
    
    return $random_string;
}

// Format date
function format_date($date, $format = 'Y-m-d') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

// Get current semester
function get_current_semester() {
    $month = date('n');
    
    if ($month >= 1 && $month <= 5) {
        return 2; // Second semester
    } else {
        return 1; // First semester
    }
}

// Calculate GPA
function calculate_gpa($grades) {
    if (empty($grades)) return 0;
    
    $total_points = 0;
    $total_credits = 0;
    
    $grade_points = [
        'A+' => 4.0, 'A' => 4.0, 'A-' => 3.7,
        'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
        'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7,
        'D+' => 1.3, 'D' => 1.0, 'F' => 0.0
    ];
    
    foreach ($grades as $grade) {
        if (isset($grade_points[$grade['grade']])) {
            $total_points += $grade_points[$grade['grade']] * $grade['credits'];
            $total_credits += $grade['credits'];
        }
    }
    
    return $total_credits > 0 ? round($total_points / $total_credits, 2) : 0;
}
?>