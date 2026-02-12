<?php
require_once '../includes/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in as student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    $course_id = isset($input['course_id']) ? intval($input['course_id']) : 0;
    $student_id = $_SESSION['user_id'];
    
    if ($course_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid course ID']);
        exit();
    }
    
    // Removed the check for already enrolled courses to allow multiple enrollments
    // $check_sql = "SELECT * FROM Enrollments WHERE student_id = ? AND course_id = ? AND enrollment_status = 'Registered'";
    // $stmt = $conn->prepare($check_sql);
    // $stmt->bind_param("ii", $student_id, $course_id);
    // $stmt->execute();
    // if ($stmt->get_result()->num_rows > 0) {
    //     echo json_encode(['success' => false, 'message' => 'Already enrolled in this course']);
    //     exit();
    // }
    
    // Check course availability
    $course_sql = "SELECT max_students, current_enrollment, status FROM Courses WHERE course_id = ?";
    $stmt = $conn->prepare($course_sql);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $course_result = $stmt->get_result();
    
    if ($course_result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Course not found']);
        exit();
    }
    
    $course = $course_result->fetch_assoc();
    
    if ($course['status'] != 'Open') {
        echo json_encode(['success' => false, 'message' => 'Course is not open for enrollment']);
        exit();
    }
    
    if ($course['current_enrollment'] >= $course['max_students']) {
        // Add student to waitlist
        $waitlist_sql = "INSERT INTO Waitlist (student_id, course_id, added_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($waitlist_sql);
        $stmt->bind_param("ii", $student_id, $course_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Course is full. You have been added to the waitlist.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add to waitlist.']);
        }
        exit();
    }
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Enroll student
        $enroll_sql = "INSERT INTO Enrollments (student_id, course_id, enrollment_status, academic_year) 
                       VALUES (?, ?, 'Registered', '2024/2025')";
        $stmt = $conn->prepare($enroll_sql);
        $stmt->bind_param("ii", $student_id, $course_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to enroll in course");
        }
        
        // Update course enrollment count
        $update_sql = "UPDATE Courses SET current_enrollment = current_enrollment + 1 WHERE course_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $course_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update course enrollment");
        }
        
        // Check if course is now full
        if ($course['current_enrollment'] + 1 >= $course['max_students']) {
            $update_status_sql = "UPDATE Courses SET status = 'Full' WHERE course_id = ?";
            $stmt = $conn->prepare($update_status_sql);
            $stmt->bind_param("i", $course_id);
            $stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Successfully enrolled in course!'
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>