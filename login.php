<?php
require_once 'includes/db.php';

$page_title = "Login";
$page_icon = "fas fa-sign-in-alt";

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $user_type = $_POST['user_type'] ?? '';
    
    if ($user_type == 'admin') {
        // Admin login
        $sql = "SELECT * FROM Admin_Users WHERE email = ? AND status = 'Active'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            
            if (password_verify($password, $admin['password'])) {
                $_SESSION['user_id'] = $admin['admin_id'];
                $_SESSION['user_type'] = 'admin';
                $_SESSION['full_name'] = $admin['full_name'];
                $_SESSION['email'] = $admin['email'];
                
                // Update last login
                $update_sql = "UPDATE Admin_Users SET last_login = NOW() WHERE admin_id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("i", $admin['admin_id']);
                $update_stmt->execute();
                
                header("Location: dashboard_admin.php");
                exit();
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "Admin not found";
        }
    } elseif ($user_type == 'student') {
        // Student login
        $sql = "SELECT * FROM Students WHERE email = ? AND status = 'Active'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $student = $result->fetch_assoc();
            
            if (password_verify($password, $student['password'])) {
                $_SESSION['user_id'] = $student['student_id'];
                $_SESSION['user_type'] = 'student';
                $_SESSION['full_name'] = $student['full_name'];
                $_SESSION['email'] = $student['email'];
                $_SESSION['registration_no'] = $student['registration_no'];
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "Student not found";
        }
    } else {
        $error = "Please select user type";
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Login to System</h4>
            </div>
            <div class="card-body">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label for="user_type" class="form-label">Login as:</label>
                        <select class="form-select" id="user_type" name="user_type" required>
                            <option value="">Select User Type</option>
                            <option value="student">Student</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="Enter your email" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Enter password" required>
                            <button class="btn btn-outline-secondary" type="button" 
                                    onclick="togglePasswordVisibility('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <p class="mb-2">Don't have an account?</p>
                    <a href="register.php" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i>Register as Student
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-body">
                <h6><i class="fas fa-info-circle me-2"></i>Demo Credentials</h6>
                <p class="mb-0"><strong>Student:</strong> Register first or use test@vau.ac.lk / password123</p>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId) {
    var input = document.getElementById(inputId);
    var button = input.parentNode.querySelector('button');
    var icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>

<?php include 'includes/footer.php'; ?>