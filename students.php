<?php
require_once 'includes/db.php';

// Check admin login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit();
}

$page_title = "Student Management";
$page_icon = "fas fa-users";

// Handle delete student
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $student_id = $_GET['id'];
    
    // Check if student has enrollments
    $check_sql = "SELECT COUNT(*) as count FROM Enrollments WHERE student_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] > 0) {
        // Delete enrollments first
        $delete_enroll_sql = "DELETE FROM Enrollments WHERE student_id = ?";
        $stmt = $conn->prepare($delete_enroll_sql);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
    }
    
    // Delete student
    $delete_sql = "DELETE FROM Students WHERE student_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $student_id);
    
    if ($stmt->execute()) {
        $success = "Student deleted successfully!";
    } else {
        $error = "Failed to delete student!";
    }
}

// Get search filters
$search = $_GET['search'] ?? '';
$faculty = $_GET['faculty'] ?? '';
$status = $_GET['status'] ?? '';

// Build query
$sql = "SELECT * FROM Students WHERE 1=1 ";
if (!empty($search)) {
    $sql .= "AND (registration_no LIKE '%$search%' OR full_name LIKE '%$search%' OR email LIKE '%$search%') ";
}
if (!empty($faculty)) {
    $sql .= "AND faculty = '$faculty' ";
}
if (!empty($status)) {
    $sql .= "AND status = '$status' ";
}
$sql .= "ORDER BY enrollment_date DESC";

$students = $conn->query($sql);

// Get faculties for filter
$faculties = $conn->query("SELECT DISTINCT faculty FROM Students WHERE faculty IS NOT NULL");

include 'includes/header.php';
?>

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStudentForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_student_id" name="student_id">
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" id="edit_phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Faculty *</label>
                        <select class="form-select" id="edit_faculty" name="faculty" required>
                            <option value="">Select Faculty</option>
                            <option value="Applied Science">Applied Science</option>
                            <option value="Business Studies">Business Studies</option>
                            <option value="Technology Studies">Technology Studies</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Students</h5>
                    <button class="btn btn-primary btn-sm" onclick="addNewStudent()">
                        <i class="fas fa-plus me-1"></i>Add Student
                    </button>
                </div>
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
                
                <form method="GET" action="students.php" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search by name, reg no, or email" 
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
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            <option value="Active" <?php echo ($status == 'Active') ? 'selected' : ''; ?>>Active</option>
                            <option value="Inactive" <?php echo ($status == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Students List
                    <span class="badge bg-primary"><?php echo $students->num_rows; ?> Students</span>
                </h5>
            </div>
            <div class="card-body">
                <?php if($students->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Reg No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Faculty</th>
                                    <th>Year</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($student = $students->fetch_assoc()): 
                                    $status_color = $student['status'] == 'Active' ? 'success' : 'danger';
                                ?>
                                <tr>
                                    <td><?php echo $student['registration_no']; ?></td>
                                    <td><?php echo $student['full_name']; ?></td>
                                    <td><?php echo $student['email']; ?></td>
                                    <td><?php echo $student['phone']; ?></td>
                                    <td><?php echo $student['faculty']; ?></td>
                                    <td>Year <?php echo $student['year_of_study']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $status_color; ?>">
                                            <?php echo $student['status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d', strtotime($student['enrollment_date'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary"
                                                    onclick="viewStudent(<?php echo $student['student_id']; ?>)"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning"
                                                    onclick="editStudent(<?php echo $student['student_id']; ?>)"
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if($student['status'] == 'Active'): ?>
                                                <button type="button" class="btn btn-outline-danger"
                                                        onclick="deactivateStudent(<?php echo $student['student_id']; ?>)"
                                                        title="Deactivate">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline-success"
                                                        onclick="activateStudent(<?php echo $student['student_id']; ?>)"
                                                        title="Activate">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-outline-danger"
                                                    onclick="deleteStudent(<?php echo $student['student_id']; ?>, '<?php echo $student['full_name']; ?>')"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No students found matching your criteria.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function addNewStudent() {
    alert('Add new student feature will be implemented in next version');
}

function viewStudent(studentId) {
    alert('View student details feature will be implemented in next version');
}

function editStudent(studentId) {
    fetch('api/get_student.php?id=' + studentId)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('edit_student_id').value = data.data.student_id;
                document.getElementById('edit_full_name').value = data.data.full_name;
                document.getElementById('edit_email').value = data.data.email;
                document.getElementById('edit_phone').value = data.data.phone;
                document.getElementById('edit_faculty').value = data.data.faculty;
                document.getElementById('edit_status').value = data.data.status;
                
                new bootstrap.Modal(document.getElementById('editStudentModal')).show();
            } else {
                showToast('Student data not found', 'danger');
            }
        });
}

function deactivateStudent(studentId) {
    if(confirm('Are you sure you want to deactivate this student?')) {
        fetch('api/update_student_status.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + studentId + '&status=Inactive'
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showToast('Student deactivated successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message, 'danger');
            }
        });
    }
}

function activateStudent(studentId) {
    if(confirm('Are you sure you want to activate this student?')) {
        fetch('api/update_student_status.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + studentId + '&status=Active'
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showToast('Student activated successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message, 'danger');
            }
        });
    }
}

function deleteStudent(studentId, studentName) {
    if(confirm('Are you sure you want to delete student: ' + studentName + '?\nThis action cannot be undone!')) {
        window.location.href = 'students.php?action=delete&id=' + studentId;
    }
}

// Handle edit form submission
document.getElementById('editStudentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('api/update_student.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showToast('Student updated successfully!', 'success');
            document.getElementById('editStudentModal').querySelector('.btn-close').click();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message, 'danger');
        }
    })
    .catch(error => {
        showToast('Error updating student', 'danger');
    });
});
</script>

<?php include 'includes/footer.php'; ?>