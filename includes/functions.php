<?php
// Admin functions
function addStudent($conn, $data) {
    $sql = "INSERT INTO Students (registration_no, full_name, nic, email, phone, date_of_birth, gender, faculty, degree_program, year_of_study, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $hashed_password = password_hash('password123', PASSWORD_DEFAULT);
    
    $stmt->bind_param("sssssssssis", 
        $data['registration_no'],
        $data['full_name'],
        $data['nic'],
        $data['email'],
        $data['phone'],
        $data['date_of_birth'],
        $data['gender'],
        $data['faculty'],
        $data['degree_program'],
        $data['year_of_study'],
        $hashed_password
    );
    
    return $stmt->execute();
}

function updateStudent($conn, $id, $data) {
    $sql = "UPDATE Students SET 
            full_name = ?, 
            email = ?, 
            phone = ?, 
            faculty = ?, 
            degree_program = ?, 
            year_of_study = ?, 
            status = ?
            WHERE student_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssisi", 
        $data['full_name'],
        $data['email'],
        $data['phone'],
        $data['faculty'],
        $data['degree_program'],
        $data['year_of_study'],
        $data['status'],
        $id
    );
    
    return $stmt->execute();
}

function deleteStudent($conn, $id) {
    $sql = "DELETE FROM Students WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function addCourse($conn, $data) {
    $sql = "INSERT INTO Courses (course_code, course_name, credits, faculty, semester, academic_year, max_students, lecturer) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisiiis", 
        $data['course_code'],
        $data['course_name'],
        $data['credits'],
        $data['faculty'],
        $data['semester'],
        $data['academic_year'],
        $data['max_students'],
        $data['lecturer']
    );
    
    return $stmt->execute();
}

// Add validation or dropdown options for faculty to include 'Technology'
function getFaculties() {
    return ['Science', 'Arts', 'Commerce', 'Engineering', 'Technology'];
}
?>