<?php
require_once 'includes/db.php';

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit();
}

$page_title = "Student Dashboard";
$page_icon = "fas fa-tachometer-alt";

$student_id = $_SESSION['user_id'];

// Get student info
$student_sql = "SELECT * FROM Students WHERE student_id = ?";
$stmt = $conn->prepare($student_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Get enrollments count
$enroll_sql = "SELECT COUNT(*) as count FROM Enrollments WHERE student_id = ? AND enrollment_status = 'Registered'";
$stmt = $conn->prepare($enroll_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$enroll_count = $stmt->get_result()->fetch_assoc()['count'];

// Get recent enrollments
$recent_sql = "SELECT c.course_code, c.course_name, c.credits, e.enrollment_date 
               FROM Enrollments e 
               JOIN Courses c ON e.course_id = c.course_id 
               WHERE e.student_id = ? AND e.enrollment_status = 'Registered'
               ORDER BY e.enrollment_date DESC LIMIT 5";
$stmt = $conn->prepare($recent_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$recent_enrollments = $stmt->get_result();

// Fetch all enrolled courses for the student
$all_courses_sql = "SELECT c.course_code, c.course_name, c.credits, e.enrollment_date 
                    FROM Enrollments e 
                    JOIN Courses c ON e.course_id = c.course_id 
                    WHERE e.student_id = ? AND e.enrollment_status = 'Registered' 
                    ORDER BY e.enrollment_date DESC";
$stmt = $conn->prepare($all_courses_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$all_courses = $stmt->get_result();

// Fetch notifications for the student
$notifications_sql = "SELECT n.message, n.created_at, c.course_name 
                      FROM Notifications n 
                      JOIN Courses c ON n.course_id = c.course_id 
                      WHERE n.student_id = ? AND n.is_read = FALSE 
                      ORDER BY n.created_at DESC";
$stmt = $conn->prepare($notifications_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$notifications = $stmt->get_result();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="card-title">
                            <i class="fas fa-user-graduate me-2"></i>
                            Welcome, <?php echo $student['full_name']; ?>!
                        </h3>
                        <p class="card-text">
                            Registration No: <?php echo $student['registration_no']; ?> | 
                            Faculty: <?php echo $student['faculty']; ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="courses.php" class="btn btn-light">
                            <i class="fas fa-plus me-1"></i>Enroll in Courses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="stat-number"><?php echo $student['registration_no']; ?></div>
            <div class="stat-label">Registration No</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-number"><?php echo $enroll_count; ?></div>
            <div class="stat-label">Enrolled Courses</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-number">Year <?php echo $student['year_of_study']; ?></div>
            <div class="stat-label">Year of Study</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fas fa-university"></i>
            </div>
            <div class="stat-number"><?php echo $student['faculty']; ?></div>
            <div class="stat-label">Faculty</div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Recently Enrolled Courses</h5>
            </div>
            <div class="card-body">
                <?php if($recent_enrollments->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Credits</th>
                                    <th>Enrolled Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $recent_enrollments->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['course_code']; ?></td>
                                        <td><?php echo $row['course_name']; ?></td>
                                        <td><?php echo $row['credits']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['enrollment_date'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="enrollments.php" class="btn btn-primary">View All Enrollments</a>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You are not enrolled in any courses yet. 
                        <a href="courses.php">Browse available courses</a> to get started.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Announcements</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Important Notice</h6>
                    <p>Course registration for Semester 1, 2024/2025 is now open.</p>
                </div>
                
                <div class="alert alert-info">
                    <h6><i class="fas fa-calendar-alt me-2"></i>Academic Calendar</h6>
                    <p>Examination period: January 15-30, 2025</p>
                </div>
                
                <div class="text-center mt-3">
                    <a href="courses.php" class="btn btn-success">
                        <i class="fas fa-book me-2"></i>Browse Courses
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-book-open me-2"></i>All Enrolled Courses</h5>
            </div>
            <div class="card-body">
                <?php if ($all_courses->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Credits</th>
                                    <th>Enrolled Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($course = $all_courses->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $course['course_code']; ?></td>
                                        <td><?php echo $course['course_name']; ?></td>
                                        <td><?php echo $course['credits']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($course['enrollment_date'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You are not enrolled in any courses yet. 
                        <a href="courses.php">Browse available courses</a> to get started.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Notifications</h5>
            </div>
            <div class="card-body">
                <?php if ($notifications->num_rows > 0): ?>
                    <div class="list-group">
                        <?php while ($notification = $notifications->fetch_assoc()): ?>
                            <a href="#" class="list-group-item list-group-item-action">
                                <?php echo $notification['message']; ?> 
                                <small class="text-muted">(Course: <?php echo $notification['course_name']; ?>, Date: <?php echo date('M d, Y', strtotime($notification['created_at'])); ?>)</small>
                            </a>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No new notifications.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>