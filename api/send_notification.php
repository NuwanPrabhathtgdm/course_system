<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'] ?? null;
    $course_id = $_POST['course_id'] ?? null;
    $message = $_POST['message'] ?? '';

    if ($student_id && $course_id && !empty($message)) {
        $sql = "INSERT INTO Notifications (student_id, course_id, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $student_id, $course_id, $message);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Notification sent successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send notification.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>