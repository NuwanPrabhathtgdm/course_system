<?php
require_once 'includes/db.php';

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit();
}

$page_title = "Enrollment History";
$page_icon = "fas fa-history";

$student_id = $_SESSION['user_id'];

// Fetch enrollment history
$history_sql = "SELECT c.course_code, c.course_name, c.credits, e.enrollment_date, e.grade 
                FROM Enrollments e 
                JOIN Courses c ON e.course_id = c.course_id 
                WHERE e.student_id = ? 
                ORDER BY e.enrollment_date DESC";
$stmt = $conn->prepare($history_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$history = $stmt->get_result();

include 'includes/header.php';
?>

<div class="container">
    <h1>Enrollment History</h1>
    <?php if ($history->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Credits</th>
                    <th>Enrollment Date</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $history->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['course_code']; ?></td>
                        <td><?php echo $row['course_name']; ?></td>
                        <td><?php echo $row['credits']; ?></td>
                        <td><?php echo $row['enrollment_date']; ?></td>
                        <td><?php echo $row['grade'] ?? 'N/A'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No enrollment history found.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>