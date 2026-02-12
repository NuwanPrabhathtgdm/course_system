<?php
require_once 'includes/db.php';

$page_title = "Student Registration";
$page_icon = "fas fa-user-plus";

include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $nic = $_POST['nic'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $faculty = $_POST['faculty'];
    $degree_program = $_POST['degree_program'];
    $year_of_study = $_POST['year_of_study'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters!";
    } else {
        // Check if email exists
        $check_sql = "SELECT student_id FROM Students WHERE email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            // Generate registration number
            $year = date('y');
            $faculty_code = get_faculty_code($faculty);
            $reg_no = generate_registration_no($conn, $faculty_code, $year);
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert student
            $sql = "INSERT INTO Students (registration_no, full_name, nic, email, phone, 
                    date_of_birth, gender, faculty, degree_program, year_of_study, password) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssssis", $reg_no, $full_name, $nic, $email, $phone, 
                             $date_of_birth, $gender, $faculty, $degree_program, 
                             $year_of_study, $hashed_password);
            
            if ($stmt->execute()) {
                $success = "Registration successful! Your Registration Number is: <strong>$reg_no</strong>";
                $_POST = array(); // Clear form
            } else {
                $error = "Registration failed: " . $conn->error;
            }
        }
    }
}

function get_faculty_code($faculty) {
    $codes = [
        'Faculty of Applied Science' => 'FAS',
        'Faculty of Business Studies' => 'FBS',
        'Faculty of Technological Studies' => 'FTS'
    ];
    return $codes[$faculty] ?? 'FAS';
}

function generate_registration_no($conn, $faculty_code, $year) {
    $prefix = "UV/$faculty_code/20$year/";
    
    $sql = "SELECT registration_no FROM Students WHERE registration_no LIKE ? 
            ORDER BY registration_no DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $like_pattern = $prefix . "%";
    $stmt->bind_param("s", $like_pattern);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $last_no = $result->fetch_assoc()['registration_no'];
        $last_num = intval(substr($last_no, -3));
        $new_num = str_pad($last_num + 1, 3, '0', STR_PAD_LEFT);
    } else {
        $new_num = '001';
    }
    
    return $prefix . $new_num;
}
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Student Registration Form</h4>
            </div>
            <div class="card-body">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($success)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        <p class="mt-2 mb-0">
                            <a href="login.php" class="btn btn-sm btn-primary">Click here to login</a>
                        </p>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="register.php" onsubmit="return validateForm()">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" class="form-control" name="full_name" 
                                       value="<?php echo $_POST['full_name'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">NIC *</label>
                                <input type="text" class="form-control" name="nic" 
                                       value="<?php echo $_POST['nic'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Date of Birth *</label>
                                <input type="date" class="form-control" name="date_of_birth" 
                                       value="<?php echo $_POST['date_of_birth'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Gender *</label>
                                <select class="form-select" name="gender" required>
                                    <option value="">Select</option>
                                    <option value="Male" <?php echo ($_POST['gender'] ?? '') == 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($_POST['gender'] ?? '') == 'Female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo ($_POST['gender'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?php echo $_POST['email'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Phone *</label>
                                <input type="tel" class="form-control" name="phone" 
                                       value="<?php echo $_POST['phone'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Faculty *</label>
                                <select class="form-select" name="faculty" required>
                                    <option value="">Select Faculty</option>
                                    <option value="Faculty of Applied Science" <?php echo ($_POST['faculty'] ?? '') == 'Faculty of Applied Science' ? 'selected' : ''; ?>>Faculty of Applied Science</option>
                                    <option value="Faculty of Business Studies" <?php echo ($_POST['faculty'] ?? '') == 'Faculty of Business Studies' ? 'selected' : ''; ?>>Faculty of Business Studies</option>
                                    <option value="Faculty of Technological Studies" <?php echo ($_POST['faculty'] ?? '') == 'Faculty of Technological Studies' ? 'selected' : ''; ?>>Faculty of Technological Studies</option>
                                </select>
                            </div>
                
                            <div class="mb-3">
                                <label class="form-label">Degree Program *</label>
                                <input type="text" class="form-control" name="degree_program" 
                                       value="<?php echo $_POST['degree_program'] ?? ''; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Year of Study *</label>
                                <select class="form-select" name="year_of_study" required>
                                    <option value="">Select Year</option>
                                    <option value="1" <?php echo ($_POST['year_of_study'] ?? '') == '1' ? 'selected' : ''; ?>>First Year</option>
                                    <option value="2" <?php echo ($_POST['year_of_study'] ?? '') == '2' ? 'selected' : ''; ?>>Second Year</option>
                                    <option value="3" <?php echo ($_POST['year_of_study'] ?? '') == '3' ? 'selected' : ''; ?>>Third Year</option>
                                    <option value="4" <?php echo ($_POST['year_of_study'] ?? '') == '4' ? 'selected' : ''; ?>>Fourth Year</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Password *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePasswordVisibility('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimum 8 characters</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Confirm Password *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePasswordVisibility('confirm_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the terms and conditions
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Register Now
                        </button>
                        <a href="login.php" class="btn btn-outline-secondary">
                            Already have an account? Login here
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function validateForm() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password.length < 8) {
        alert('Password must be at least 8 characters long!');
        return false;
    }
    
    if (password !== confirmPassword) {
        alert('Passwords do not match!');
        return false;
    }
    
    return true;
}

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