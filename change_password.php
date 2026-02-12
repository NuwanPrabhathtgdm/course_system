<?php
require_once 'includes/db.php';

// Check login - Allow both student and admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$page_title = "Change Password";
$page_icon = "fas fa-key";

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Initialize variables
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required!";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
    } elseif (strlen($new_password) < 8) {
        $error = "New password must be at least 8 characters!";
    } else {
        // Get current password based on user type
        if ($user_type == 'student') {
            $sql = "SELECT student_id, password FROM Students WHERE student_id = ?";
            $table = 'Students';
            $id_field = 'student_id';
        } elseif ($user_type == 'admin') {
            $sql = "SELECT admin_id, password FROM Admin_Users WHERE admin_id = ?";
            $table = 'Admin_Users';
            $id_field = 'admin_id';
        } else {
            $error = "Invalid user type!";
        }
        
        if (!empty($sql)) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                
                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();
                    
                    // Verify current password
                    if (password_verify($current_password, $user['password'])) {
                        // Update password
                        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $update_sql = "UPDATE $table SET password = ? WHERE $id_field = ?";
                        $update_stmt = $conn->prepare($update_sql);
                        $update_stmt->bind_param("si", $new_hashed_password, $user_id);
                        
                        if ($update_stmt->execute()) {
                            $success = "Password changed successfully!";
                            $_POST = array(); // Clear form
                        } else {
                            $error = "Failed to update password! Database error.";
                        }
                    } else {
                        $error = "Current password is incorrect!";
                        // Debug hint for default password
                        if (password_verify('password123', $user['password'])) {
                            $error .= " (Hint: Try 'password123' as current password)";
                        }
                    }
                } else {
                    $error = "User not found in database!";
                }
            } else {
                $error = "Database query failed!";
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-key me-2"></i>Change Password</h5>
                <p class="mb-0 small">
                    <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['full_name']); ?> 
                    (<?php echo htmlspecialchars($user_type); ?>)
                </p>
            </div>
            <div class="card-body">
                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        <div class="mt-2">
                            <?php if($user_type == 'student'): ?>
                                <a href="dashboard.php" class="btn btn-sm btn-primary">
                                    <i class="fas fa-tachometer-alt me-1"></i>Back to Dashboard
                                </a>
                            <?php else: ?>
                                <a href="dashboard_admin.php" class="btn btn-sm btn-primary">
                                    <i class="fas fa-cogs me-1"></i>Back to Admin Dashboard
                                </a>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="change_password.php" id="passwordForm">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">
                            <i class="fas fa-lock me-1"></i>Current Password *
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="current_password" 
                                   name="current_password" required
                                   placeholder="Enter current password">
                            <button class="btn btn-outline-secondary" type="button" 
                                    onclick="togglePasswordVisibility('current_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">
                            <i class="fas fa-key me-1"></i>New Password *
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" 
                                   name="new_password" required
                                   placeholder="Enter new password (min 8 characters)"
                                   minlength="8">
                            <button class="btn btn-outline-secondary" type="button" 
                                    onclick="togglePasswordVisibility('new_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">Minimum 8 characters with letters and numbers</small>
                        <div class="progress mt-1" style="height: 5px;">
                            <div class="progress-bar" id="passwordStrength" role="progressbar"></div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">
                            <i class="fas fa-key me-1"></i>Confirm New Password *
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm_password" 
                                   name="confirm_password" required
                                   placeholder="Confirm new password"
                                   minlength="8">
                            <button class="btn btn-outline-secondary" type="button" 
                                    onclick="togglePasswordVisibility('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small id="passwordMatch" class="text-muted"></small>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Update Password
                        </button>
                        <?php if($user_type == 'student'): ?>
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                        <?php else: ?>
                            <a href="dashboard_admin.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-body">
                <h6><i class="fas fa-question-circle me-2"></i>Need Help?</h6>
                <ul class="mb-0 small">
                    <li>If you forgot your current password, contact administrator</li>
                    <li>Default password for new students: <code>password123</code></li>
                    <li>Default password for admin: <code>password123</code></li>
                    <li>Always use strong passwords with mix of characters</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Password strength indicator
document.getElementById('new_password').addEventListener('input', function() {
    var password = this.value;
    var strengthBar = document.getElementById('passwordStrength');
    var strength = 0;
    
    if (password.length >= 8) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password)) strength += 25;
    if (/[^A-Za-z0-9]/.test(password)) strength += 25;
    
    strengthBar.style.width = strength + '%';
    
    if (strength < 50) {
        strengthBar.className = 'progress-bar bg-danger';
    } else if (strength < 75) {
        strengthBar.className = 'progress-bar bg-warning';
    } else {
        strengthBar.className = 'progress-bar bg-success';
    }
});

// Password match checker
function checkPasswordMatch() {
    var newPass = document.getElementById('new_password').value;
    var confirmPass = document.getElementById('confirm_password').value;
    var matchText = document.getElementById('passwordMatch');
    
    if (confirmPass === '') {
        matchText.innerHTML = '';
        return;
    }
    
    if (newPass === confirmPass) {
        matchText.innerHTML = '<i class="fas fa-check text-success me-1"></i>Passwords match';
        matchText.className = 'text-success';
    } else {
        matchText.innerHTML = '<i class="fas fa-times text-danger me-1"></i>Passwords do not match';
        matchText.className = 'text-danger';
    }
}

document.getElementById('new_password').addEventListener('input', checkPasswordMatch);
document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);

// Form validation
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    var newPass = document.getElementById('new_password').value;
    var confirmPass = document.getElementById('confirm_password').value;
    
    if (newPass.length < 8) {
        e.preventDefault();
        showToast('Password must be at least 8 characters long!', 'danger');
        return false;
    }
    
    if (newPass !== confirmPass) {
        e.preventDefault();
        showToast('Passwords do not match!', 'danger');
        return false;
    }
    
    return true;
});

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