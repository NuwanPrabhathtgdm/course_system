-- Create Database
CREATE DATABASE IF NOT EXISTS uv_registration;
USE uv_registration;

-- Students Table
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Courses Table
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Enrollments Table
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
    FOREIGN KEY (course_id) REFERENCES Courses(course_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS Admin_Users (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('Super Admin', 'Faculty Admin', 'Registrar') DEFAULT 'Faculty Admin',
    password VARCHAR(255) NOT NULL,
    last_login DATETIME,
    status ENUM('Active', 'Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Default Admin (Password: password123)
INSERT INTO Admin_Users (username, full_name, email, role, password) VALUES
('admin', 'System Administrator', 'admin@vau.ac.lk', 'Super Admin', 
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE 
password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

-- Insert Sample Courses
INSERT INTO Courses (course_code, course_name, credits, faculty, semester, academic_year, lecturer, max_students) VALUES
('CS111', 'Introduction to Programming', 3, 'Faculty of Technological Studies', 1, '2024/2025', 'Dr. Kamal Perera', 60),
('MA101', 'Calculus I', 3, 'Faculty of Applied Science', 1, '2024/2025', 'Dr. Sunil Rathnayake', 70),
('EN101', 'Technical English', 2, 'Faculty of Humanities', 1, '2024/2025', 'Dr. Maria Silva', 60),
('BS201', 'Principles of Management', 3, 'Faculty of Business Studies', 2, '2024/2025', 'Prof. Nimal Fernando', 55),
('CS211', 'Data Structures and Algorithms', 4, 'Faculty of Technological Studies', 2, '2024/2025', 'Dr. Anil Jayasuriya', 50),
('MA201', 'Linear Algebra', 3, 'Faculty of Applied Science', 2, '2024/2025', 'Dr. Priya Ranasinghe', 65),
('SS101', 'Sociology and Society', 2, 'Faculty of Humanities', 1, '2024/2025', 'Dr. Suresh Kumar', 60)
ON DUPLICATE KEY UPDATE course_code = course_code;

-- Insert Sample Student (Password: password123)
INSERT INTO Students (registration_no, full_name, nic, email, phone, date_of_birth, gender, faculty, degree_program, year_of_study, password) VALUES
('UV/FAS/2024/001', 'Test Student', '200012345678', 'test@vau.ac.lk', '0771234567', '2000-01-01', 'Male', 'Faculty of Applied Science', 'B.Sc. Physical Science', 1, 
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE email = email;

-- Create Indexes
CREATE INDEX IF NOT EXISTS idx_student_email ON Students(email);
CREATE INDEX IF NOT EXISTS idx_enrollment_student ON Enrollments(student_id);
CREATE INDEX IF NOT EXISTS idx_enrollment_course ON Enrollments(course_id);
CREATE INDEX IF NOT EXISTS idx_course_faculty ON Courses(faculty);