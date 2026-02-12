<?php
require_once 'includes/db.php';

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit();
}

$page_title = "Course Feedback";
$page_icon = "fas fa-comments";

$student_id = $_SESSION['user_id'];

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'] ?? null;
    $feedback = $_POST['feedback'] ?? '';

    if ($course_id && !empty($feedback)) {
        $feedback_sql = "INSERT INTO Feedback (student_id, course_id, feedback, submitted_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($feedback_sql);
        $stmt->bind_param("iis", $student_id, $course_id, $feedback);

        if ($stmt->execute()) {
            $success_message = "Feedback submitted successfully.";
        } else {
            $error_message = "Failed to submit feedback.";
        }
    } else {
        $error_message = "Please select a course and provide feedback.";
    }
}

// Fetch enrolled courses
$courses_sql = "SELECT c.course_id, c.course_name FROM Enrollments e JOIN Courses c ON e.course_id = c.course_id WHERE e.student_id = ?";
$stmt = $conn->prepare($courses_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$courses = $stmt->get_result();

include 'includes/header.php';
?>

<div class="container">
    <h1>Course Feedback</h1>
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"> <?php echo $success_message; ?> </div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"> <?php echo $error_message; ?> </div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="course_id" class="form-label">Course</label>
            <select class="form-control" id="course_id" name="course_id">
                <option value="">Select a course</option>
                <?php while ($course = $courses->fetch_assoc()): ?>
                    <option value="<?php echo $course['course_id']; ?>">
                        <?php echo $course['course_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="feedback" class="form-label">Feedback</label>
            <textarea class="form-control" id="feedback" name="feedback" rows="4"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Feedback</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>