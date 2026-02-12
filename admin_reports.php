<?php
require_once 'includes/db.php';

// Check admin login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit();
}

$page_title = "Admin Reports";
$page_icon = "fas fa-chart-bar";

// Fetch enrollment statistics
$enrollment_stats_sql = "SELECT c.course_name, COUNT(e.enrollment_id) as total_enrollments 
                         FROM Enrollments e 
                         JOIN Courses c ON e.course_id = c.course_id 
                         GROUP BY c.course_id 
                         ORDER BY total_enrollments DESC";
$enrollment_stats = $conn->query($enrollment_stats_sql);

include 'includes/header.php';
?>

<div class="container">
    <h1>Admin Reports</h1>
    <h3>Enrollment Statistics</h3>
    <?php if ($enrollment_stats->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Total Enrollments</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $enrollment_stats->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['course_name']; ?></td>
                        <td><?php echo $row['total_enrollments']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No enrollment data available.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>