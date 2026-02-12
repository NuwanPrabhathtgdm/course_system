-- Sample Data for University of Vavuniya Course Registration System

USE uv_registration;

-- Insert more sample courses
INSERT INTO Courses (course_code, course_name, credits, faculty, semester, academic_year, lecturer, max_students, description) VALUES
('PH101', 'Physics I', 3, 'Faculty of Applied Science', 1, '2024/2025', 'Dr. Rajitha Silva', 65, 'Fundamental principles of mechanics and thermodynamics'),
('CH101', 'Chemistry I', 3, 'Faculty of Applied Science', 1, '2024/2025', 'Prof. Anoma Perera', 60, 'Basic concepts of inorganic and physical chemistry'),
('IT101', 'Information Technology Fundamentals', 3, 'Faculty of Technological Studies', 1, '2024/2025', 'Dr. Sanjaya Bandara', 70, 'Introduction to computer systems and IT concepts'),
('EC101', 'Principles of Economics', 3, 'Faculty of Business Studies', 1, '2024/2025', 'Dr. Priyantha Fernando', 55, 'Micro and macro economic principles'),
('EN201', 'Advanced English', 2, 'Faculty of Humanities', 2, '2024/2025', 'Dr. Nirmala De Silva', 60, 'Advanced English language and communication skills'),
('MA301', 'Advanced Calculus', 4, 'Faculty of Applied Science', 3, '2024/2025', 'Prof. Ranjith Wickramasinghe', 50, 'Multivariable calculus and vector analysis'),
('CS301', 'Database Systems', 4, 'Faculty of Technological Studies', 3, '2024/2025', 'Dr. Chathura Gunasekara', 45, 'Design and implementation of database systems'),
('MK201', 'Marketing Management', 3, 'Faculty of Business Studies', 2, '2024/2025', 'Dr. Samantha Rathnayake', 50, 'Principles and practices of marketing'),
('SS201', 'Social Research Methods', 3, 'Faculty of Humanities', 2, '2024/2025', 'Dr. Kanthi Perera', 40, 'Quantitative and qualitative research methods');

-- Insert more sample students
INSERT INTO Students (registration_no, full_name, nic, email, phone, date_of_birth, gender, faculty, degree_program, year_of_study, password, enrollment_date) VALUES
('UV/FBS/2024/002', 'Nimal Perera', '199812345678', 'nimal@vau.ac.lk', '0772345678', '1998-05-15', 'Male', 'Faculty of Business Studies', 'B.B.A. Business Administration', 2, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-10-01'),
('UV/FAS/2024/003', 'Kamala Silva', '199912345679', 'kamala@vau.ac.lk', '0763456789', '1999-08-20', 'Female', 'Faculty of Applied Science', 'B.Sc. Biological Science', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-01-15'),
('UV/FTS/2024/004', 'Sunil Fernando', '200012345680', 'sunil@vau.ac.lk', '0754567890', '2000-03-10', 'Male', 'Faculty of Technological Studies', 'B.Sc. Information Systems', 3, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2022-10-01'),
('UV/FAS/2024/005', 'Anoma Rathnayake', '199812345681', 'anoma@vau.ac.lk', '0715678901', '1998-11-25', 'Female', 'Faculty of Applied Science', 'B.Sc. Physical Science', 4, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2021-10-01'),
('UV/FBS/2024/006', 'Rajitha Bandara', '199912345682', 'rajitha@vau.ac.lk', '0786789012', '1999-07-30', 'Male', 'Faculty of Business Studies', 'B.Sc. Finance', 2, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-10-01');

-- Insert sample enrollments
INSERT INTO Enrollments (student_id, course_id, enrollment_status, academic_year, enrollment_date) VALUES
(2, 1, 'Registered', '2024/2025', '2024-01-10'),
(2, 3, 'Registered', '2024/2025', '2024-01-10'),
(3, 2, 'Registered', '2024/2025', '2024-01-12'),
(3, 4, 'Registered', '2024/2025', '2024-01-12'),
(4, 5, 'Registered', '2024/2025', '2024-01-15'),
(4, 6, 'Registered', '2024/2025', '2024-01-15'),
(5, 7, 'Registered', '2024/2025', '2024-01-18'),
(5, 8, 'Registered', '2024/2025', '2024-01-18');

-- Insert additional admin users
INSERT INTO Admin_Users (username, full_name, email, role, password) VALUES
('registrar', 'University Registrar', 'registrar@vau.ac.lk', 'Registrar', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('faculty_admin', 'Faculty Administrator', 'faculty@vau.ac.lk', 'Faculty Admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Update course enrollment counts
UPDATE Courses c
SET c.current_enrollment = (
    SELECT COUNT(*) 
    FROM Enrollments e 
    WHERE e.course_id = c.course_id 
    AND e.enrollment_status = 'Registered'
);

-- Create view for student enrollments
CREATE OR REPLACE VIEW StudentEnrollments AS
SELECT 
    s.student_id,
    s.registration_no,
    s.full_name,
    s.faculty,
    e.enrollment_id,
    e.course_id,
    c.course_code,
    c.course_name,
    c.credits,
    e.enrollment_status,
    e.enrollment_date,
    e.academic_year
FROM Students s
LEFT JOIN Enrollments e ON s.student_id = e.student_id
LEFT JOIN Courses c ON e.course_id = c.course_id
WHERE e.enrollment_status = 'Registered';

-- Create view for course statistics
CREATE OR REPLACE VIEW CourseStatistics AS
SELECT 
    c.course_id,
    c.course_code,
    c.course_name,
    c.faculty,
    c.max_students,
    c.current_enrollment,
    c.lecturer,
    ROUND((c.current_enrollment / c.max_students) * 100, 2) as enrollment_percentage,
    c.status
FROM Courses c;

-- Create index for performance
CREATE INDEX idx_student_faculty ON Students(faculty);
CREATE INDEX idx_course_faculty_semester ON Courses(faculty, semester);
CREATE INDEX idx_enrollment_status_date ON Enrollments(enrollment_status, enrollment_date);