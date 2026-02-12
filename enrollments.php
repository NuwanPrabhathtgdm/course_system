<?php
require_once 'includes/db.php';

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit();
}

$page_title = "My Enrollments";
$page_icon = "fas fa-clipboard-list";

$student_id = $_SESSION['user_id'];

// Handle drop course
if (isset($_GET['action']) && $_GET['action'] == 'drop' && isset($_GET['id'])) {
    $enrollment_id = $_GET['id'];
    
    $drop_sql = "UPDATE Enrollments SET enrollment_status = 'Dropped' 
                 WHERE enrollment_id = ? AND student_id = ?";
    $stmt = $conn->prepare($drop_sql);
    $stmt->bind_param("ii", $enrollment_id, $student_id);
    
    if ($stmt->execute()) {
        $success = "Course dropped successfully!";
    } else {
        $error = "Failed to drop course!";
    }
}

// Get enrollments
$sql = "SELECT e.*, c.course_code, c.course_name, c.credits, c.lecturer 
        FROM Enrollments e 
        JOIN Courses c ON e.course_id = c.course_id 
        WHERE e.student_id = ? 
        ORDER BY e.enrollment_date DESC";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$enrollments = $stmt->get_result();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>My Course Enrollments</h5>
            </div>
            <div class="card-body">
                <?php if(isset($success)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if($enrollments->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Credits</th>
                                    <th>Lecturer</th>
                                    <th>Status</th>
                                    <th>Enrollment Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $enrollments->fetch_assoc()): 
                                    $status_color = '';
                                    switch($row['enrollment_status']) {
                                        case 'Registered': $status_color = 'success'; break;
                                        case 'Pending': $status_color = 'warning'; break;
                                        case 'Dropped': $status_color = 'danger'; break;
                                        case 'Completed': $status_color = 'info'; break;
                                        default: $status_color = 'secondary';
                                    }
                                ?>
                                <tr>
                                    <td><?php echo $row['course_code']; ?></td>
                                    <td><?php echo $row['course_name']; ?></td>
                                    <td><?php echo $row['credits']; ?></td>
                                    <td><?php echo $row['lecturer']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $status_color; ?>">
                                            <?php echo $row['enrollment_status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($row['enrollment_date'])); ?></td>
                                    <td>
                                        <?php if($row['enrollment_status'] == 'Registered'): ?>
                                            <a href="enrollments.php?action=drop&id=<?php echo $row['enrollment_id']; ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Are you sure you want to drop this course?')">
                                                <i class="fas fa-times"></i> Drop
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You are not enrolled in any courses yet. 
                        <a href="courses.php">Browse courses</a> to get started.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>