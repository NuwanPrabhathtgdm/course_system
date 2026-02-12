<?php
require_once 'includes/db.php';

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit();
}

$page_title = "Profile Management";
$page_icon = "fas fa-user";

$student_id = $_SESSION['user_id'];

// Fetch student profile
$profile_sql = "SELECT * FROM Students WHERE student_id = ?";
$stmt = $conn->prepare($profile_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? $profile['email'];
    $phone = $_POST['phone'] ?? $profile['phone'];
    $address = $_POST['address'] ?? $profile['address'];

    $update_sql = "UPDATE Students SET email = ?, phone = ?, address = ? WHERE student_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssi", $email, $phone, $address, $student_id);

    if ($stmt->execute()) {
        $success_message = "Profile updated successfully.";
        $profile['email'] = $email;
        $profile['phone'] = $phone;
        $profile['address'] = $address;
    } else {
        $error_message = "Failed to update profile.";
    }
}

include 'includes/header.php';
?>

<div class="container">
    <h1>Profile Management</h1>
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"> <?php echo $success_message; ?> </div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"> <?php echo $error_message; ?> </div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $profile['email']; ?>">
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $profile['phone']; ?>">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" name="address"><?php echo $profile['address']; ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>