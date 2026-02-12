<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id(true);
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'uv_registration');

// Error reporting (for development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create connection with error handling
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // Set charset to UTF-8
    $conn->set_charset("utf8mb4");
    
    // Set timezone
    date_default_timezone_set('Asia/Colombo');
    
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}

// Function to check and create tables if not exist
function initializeDatabase($conn) {
    // Create tables if they don't exist
    $tables = [
        'Students' => "
            CREATE TABLE IF NOT EXISTS Students (
                student_id INT PRIMARY KEY AUTO_INCREMENT,
                registration_no VARCHAR(20) UNIQUE NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                nic VARCHAR(20) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                phone VARCHAR(15),
                address TEXT,
                date_of_birth DATE,
                gender ENUM('Male', 'Female', 'Other'),
                faculty VARCHAR(100),
                degree_program VARCHAR(100),
                year_of_study INT,
                enrollment_date DATE DEFAULT CURRENT_DATE,
                password VARCHAR(255) NOT NULL,
                status ENUM('Active', 'Inactive', 'Graduated') DEFAULT 'Active'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
        
        'Courses' => "
            CREATE TABLE IF NOT EXISTS Courses (
                course_id INT PRIMARY KEY AUTO_INCREMENT,
                course_code VARCHAR(20) UNIQUE NOT NULL,
                course_name VARCHAR(200) NOT NULL,
                credits INT NOT NULL,
                faculty VARCHAR(100),
                semester INT,
                academic_year VARCHAR(10),
                max_students INT DEFAULT 50,
                current_enrollment INT DEFAULT 0,
                lecturer VARCHAR(100),
                schedule_time VARCHAR(50),
                schedule_day VARCHAR(20),
                venue VARCHAR(50),
                prerequisites TEXT,
                description TEXT,
                status ENUM('Open', 'Closed', 'Full') DEFAULT 'Open'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
        
        'Enrollments' => "
            CREATE TABLE IF NOT EXISTS Enrollments (
                enrollment_id INT PRIMARY KEY AUTO_INCREMENT,
                student_id INT,
                course_id INT,
                enrollment_date DATE DEFAULT CURRENT_DATE,
                enrollment_status ENUM('Registered', 'Pending', 'Dropped', 'Completed'),
                grade VARCHAR(2) DEFAULT NULL,
                grade_points DECIMAL(3,2),
                attendance_percentage DECIMAL(5,2),
                academic_year VARCHAR(10),
                FOREIGN KEY (student_id) REFERENCES Students(student_id) ON DELETE CASCADE,
                FOREIGN KEY (course_id) REFERENCES Courses(course_id) ON DELETE CASCADE,
                UNIQUE KEY unique_enrollment (student_id, course_id, academic_year)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
        
        'Admin_Users' => "
            CREATE TABLE IF NOT EXISTS Admin_Users (
                admin_id INT PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(50) UNIQUE NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                role ENUM('Super Admin', 'Faculty Admin', 'Registrar') DEFAULT 'Faculty Admin',
                password VARCHAR(255) NOT NULL,
                last_login DATETIME,
                status ENUM('Active', 'Inactive') DEFAULT 'Active'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
        
        'Notifications' => "
            CREATE TABLE IF NOT EXISTS Notifications (
                notification_id INT PRIMARY KEY AUTO_INCREMENT,
                student_id INT,
                course_id INT,
                message TEXT NOT NULL,
                is_read BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (student_id) REFERENCES Students(student_id) ON DELETE CASCADE,
                FOREIGN KEY (course_id) REFERENCES Courses(course_id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
        
        'Waitlist' => "
            CREATE TABLE IF NOT EXISTS Waitlist (
                waitlist_id INT PRIMARY KEY AUTO_INCREMENT,
                student_id INT,
                course_id INT,
                added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (student_id) REFERENCES Students(student_id) ON DELETE CASCADE,
                FOREIGN KEY (course_id) REFERENCES Courses(course_id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
        
        'Feedback' => "
            CREATE TABLE IF NOT EXISTS Feedback (
                feedback_id INT PRIMARY KEY AUTO_INCREMENT,
                student_id INT,
                course_id INT,
                feedback TEXT NOT NULL,
                submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (student_id) REFERENCES Students(student_id) ON DELETE CASCADE,
                FOREIGN KEY (course_id) REFERENCES Courses(course_id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        "
    ];
    
    // Create each table
    foreach ($tables as $table_name => $create_sql) {
        if (!$conn->query($create_sql)) {
            error_log("Failed to create table $table_name: " . $conn->error);
        }
    }
    
    // Insert default admin user if not exists
    $check_admin = $conn->query("SELECT COUNT(*) as count FROM Admin_Users WHERE username = 'admin'");
    $admin_count = $check_admin ? $check_admin->fetch_assoc()['count'] : 0;
    
    if ($admin_count == 0) {
        $hashed_password = password_hash('password123', PASSWORD_DEFAULT);
        $conn->query("INSERT INTO Admin_Users (username, full_name, email, role, password) 
                     VALUES ('admin', 'System Administrator', 'admin@vau.ac.lk', 'Super Admin', '$hashed_password')");
    }
    
    // Insert sample courses if none exist
    $check_courses = $conn->query("SELECT COUNT(*) as count FROM Courses");
    $courses_count = $check_courses ? $check_courses->fetch_assoc()['count'] : 0;
    
    if ($courses_count == 0) {
        $sample_courses = [
            "INSERT INTO Courses (course_code, course_name, credits, faculty, semester, academic_year, lecturer, max_students) VALUES 
            ('CS111', 'Introduction to Programming', 3, 'Faculty of Technological Studies', 1, '2024/2025', 'Dr. Kamal Perera', 60),
            ('MA101', 'Calculus I', 3, 'Faculty of Applied Science', 1, '2024/2025', 'Dr. Sunil Rathnayake', 70),
            ('EN101', 'Technical English', 2, 'Faculty of Humanities', 1, '2024/2025', 'Dr. Maria Silva', 60),
            ('BS201', 'Principles of Management', 3, 'Faculty of Business Studies', 2, '2024/2025', 'Prof. Nimal Fernando', 55),
            ('CS211', 'Data Structures and Algorithms', 4, 'Faculty of Technological Studies', 2, '2024/2025', 'Dr. Anil Jayasuriya', 50)",
        ];
        
        foreach ($sample_courses as $sql) {
            $conn->query($sql);
        }
    }
}

// Initialize database on first run
initializeDatabase($conn);

// Security functions
function sanitize_input($data) {
    global $conn;
    if ($data === null) return null;
    return htmlspecialchars(strip_tags(trim($data)));
}

function check_csrf_token() {
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

// Check if user is admin
function is_admin() {
    return is_logged_in() && $_SESSION['user_type'] === 'admin';
}

// Check if user is student
function is_student() {
    return is_logged_in() && $_SESSION['user_type'] === 'student';
}

// Redirect if not logged in
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

// Redirect if not admin
function require_admin() {
    require_login();
    if (!is_admin()) {
        header("Location: dashboard.php");
        exit();
    }
}

// Redirect if not student
function require_student() {
    require_login();
    if (!is_student()) {
        header("Location: dashboard_admin.php");
        exit();
    }
}
?>