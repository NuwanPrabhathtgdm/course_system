<?php
require_once 'includes/db.php';

// Check admin login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit();
}

$page_title = "Admin Dashboard";
$page_icon = "fas fa-tachometer-alt";

// Get statistics
$students_count = $conn->query("SELECT COUNT(*) as count FROM Students")->fetch_assoc()['count'];
$courses_count = $conn->query("SELECT COUNT(*) as count FROM Courses")->fetch_assoc()['count'];
$enrollments_count = $conn->query("SELECT COUNT(*) as count FROM Enrollments WHERE enrollment_status = 'Registered'")->fetch_assoc()['count'];

// Get recent students
$recent_students = $conn->query("SELECT registration_no, full_name, email FROM Students ORDER BY enrollment_date DESC LIMIT 5");

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-dark text-white">
            <div class="card-body">
                <h3 class="card-title">
                    <i class="fas fa-cogs me-2"></i>
                    Admin Dashboard
                </h3>
                <p class="card-text">
                    Welcome, <?php echo $_SESSION['full_name']; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card stat-card bg-primary text-white">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number"><?php echo $students_count; ?></div>
            <div class="stat-label">Total Students</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card bg-success text-white">
            <div class="stat-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-number"><?php echo $courses_count; ?></div>
            <div class="stat-label">Total Courses</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card bg-info text-white">
            <div class="stat-icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="stat-number"><?php echo $enrollments_count; ?></div>
            <div class="stat-label">Active Enrollments</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card bg-warning text-white">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-number"><?php echo date('Y'); ?></div>
            <div class="stat-label">Academic Year</div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Recent Students</h5>
            </div>
            <div class="card-body">
                <?php if($recent_students->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Reg No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($student = $recent_students->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $student['registration_no']; ?></td>
                                        <td><?php echo $student['full_name']; ?></td>
                                        <td><?php echo $student['email']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="students.php" class="btn btn-sm btn-primary">View All Students</a>
                <?php else: ?>
                    <p class="text-muted">No students found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="students.php" class="btn btn-outline-primary">
                        <i class="fas fa-users me-2"></i>Manage Students
                    </a>
                    <a href="#" class="btn btn-outline-success" onclick="addNewCourse()">
                        <i class="fas fa-plus-circle me-2"></i>Add New Course
                    </a>
                    <a href="#" class="btn btn-outline-info" onclick="generateReport()">
                        <i class="fas fa-chart-bar me-2"></i>Generate Reports
                    </a>
                    <a href="#" class="btn btn-outline-warning" onclick="systemSettings()">
                        <i class="fas fa-cog me-2"></i>System Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addNewCourse() {
    alert('Add new course feature will be implemented in next version');
}

function generateReport() {
    alert('Report generation feature will be implemented in next version');
}

function systemSettings() {
    alert('System settings feature will be implemented in next version');
}
</script>

<?php include 'includes/footer.php'; ?>