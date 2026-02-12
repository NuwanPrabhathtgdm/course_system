<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $faculty = $_POST['faculty'];
    $status = $_POST['status'];
    
    // Check if email already exists for another student
    $check_sql = "SELECT student_id FROM Students WHERE email = ? AND student_id != ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("si", $email, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists for another student']);
        exit();
    }
    
    // Update student
    $sql = "UPDATE Students SET 
            full_name = ?, 
            email = ?, 
            phone = ?, 
            faculty = ?, 
            status = ?
            WHERE student_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $full_name, $email, $phone, $faculty, $status, $student_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed: ' . $conn->error]);
    }
}
?>