<?php
require_once 'includes/db.php';

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit();
}

$page_title = "Available Courses";
$page_icon = "fas fa-book";

$student_id = $_SESSION['user_id'];

// Get search filters
$search = $_GET['search'] ?? '';
$faculty = $_GET['faculty'] ?? '';
$semester = $_GET['semester'] ?? '';
$academic_year = $_GET['academic_year'] ?? '';

// Build query
$sql = "SELECT * FROM Courses WHERE status = 'Open' ";
if (!empty($search)) {
    $sql .= "AND (course_code LIKE '%$search%' OR course_name LIKE '%$search%' OR lecturer LIKE '%$search%') ";
}
if (!empty($faculty)) {
    $sql .= "AND faculty = '$faculty' ";
}
if (!empty($semester)) {
    $sql .= "AND semester = '$semester' ";
}
if (!empty($academic_year)) {
    $sql .= "AND academic_year = '$academic_year' ";
}
$sql .= "ORDER BY course_code";

$courses = $conn->query($sql);

// Get enrolled courses
$enrolled_sql = "SELECT course_id FROM Enrollments WHERE student_id = ? AND enrollment_status = 'Registered'";
$stmt = $conn->prepare($enrolled_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$enrolled_result = $stmt->get_result();
$enrolled_courses = [];
while($row = $enrolled_result->fetch_assoc()) {
    $enrolled_courses[] = $row['course_id'];
}

// Get faculties, semesters, and academic years for filter
$faculties = $conn->query("SELECT DISTINCT faculty FROM Courses");
$semesters = $conn->query("SELECT DISTINCT semester FROM Courses");
$academic_years = $conn->query("SELECT DISTINCT academic_year FROM Courses");

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Courses</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="courses.php" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search by course code, name, or lecturer" 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="faculty">
                            <option value="">All Faculties</option>
                            <?php while($row = $faculties->fetch_assoc()): ?>
                                <option value="<?php echo $row['faculty']; ?>"
                                    <?php echo ($faculty == $row['faculty']) ? 'selected' : ''; ?>>
                                    <?php echo $row['faculty']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="semester">
                            <option value="">All Semesters</option>
                            <?php while($row = $semesters->fetch_assoc()): ?>
                                <option value="<?php echo $row['semester']; ?>"
                                    <?php echo ($semester == $row['semester']) ? 'selected' : ''; ?>>
                                    Semester <?php echo $row['semester']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="academic_year">
                            <option value="">All Academic Years</option>
                            <?php while($row = $academic_years->fetch_assoc()): ?>
                                <option value="<?php echo $row['academic_year']; ?>"
                                    <?php echo ($academic_year == $row['academic_year']) ? 'selected' : ''; ?>>
                                    <?php echo $row['academic_year']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php if($courses->num_rows > 0): ?>
        <?php while($course = $courses->fetch_assoc()): 
            $is_enrolled = in_array($course['course_id'], $enrolled_courses);
            $is_full = $course['current_enrollment'] >= $course['max_students'];
        ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?php echo $course['course_code']; ?></h5>
                </div>
                <div class="card-body">
                    <h6><?php echo $course['course_name']; ?></h6>
                    
                    <div class="course-details mt-3">
                        <p><i class="fas fa-chalkboard-teacher me-2"></i>
                            <?php echo $course['lecturer']; ?>
                        </p>
                        
                        <p><i class="fas fa-university me-2"></i>
                            <?php echo $course['faculty']; ?>
                        </p>
                        
                        <div class="row">
                            <div class="col-6">
                                <p><i class="fas fa-star me-2"></i>
                                    <?php echo $course['credits']; ?> Credits
                                </p>
                            </div>
                            <div class="col-6">
                                <p><i class="fas fa-calendar-alt me-2"></i>
                                    Semester <?php echo $course['semester']; ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="enrollment-status mt-3">
                            <div class="progress mb-2">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: <?php echo ($course['current_enrollment'] / $course['max_students']) * 100; ?>%">
                                </div>
                            </div>
                            <small class="text-muted">
                                <?php echo $course['current_enrollment']; ?> / <?php echo $course['max_students']; ?> seats
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-outline-primary"
                                onclick="viewCourseDetails(<?php echo $course['course_id']; ?>)">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        
                        <?php if($is_enrolled): ?>
                            <button class="btn btn-sm btn-success" disabled>
                                <i class="fas fa-check"></i> Enrolled
                            </button>
                        <?php elseif($is_full): ?>
                            <button class="btn btn-sm btn-danger" disabled>
                                <i class="fas fa-times"></i> Full
                            </button>
                        <?php else: ?>
                            <button class="btn btn-sm btn-primary"
                                    onclick="enrollCourse(<?php echo $course['course_id']; ?>)">
                                <i class="fas fa-plus"></i> Enroll
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No courses found matching your criteria.
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function enrollCourse(courseId) {
    if(confirm('Are you sure you want to enroll in this course?')) {
        fetch('api/enroll_course.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'course_id=' + courseId + '&student_id=<?php echo $student_id; ?>'
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showToast('Successfully enrolled in course!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Enrollment failed', 'danger');
            }
        })
        .catch(error => {
            showToast('Network error', 'danger');
        });
    }
}

function viewCourseDetails(courseId) {
    // In full version, this would show a modal with course details
    alert('Course details feature will be implemented in next version');
}
</script>

<?php include 'includes/footer.php'; ?>