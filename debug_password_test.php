<?php
require_once 'includes/db.php';

echo "<h2>Password Debug Test</h2>";
echo "<pre>";

// Check session
echo "Session User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo "Session User Type: " . ($_SESSION['user_type'] ?? 'NOT SET') . "\n";

// Test password hashing
$test_pass = "password123";
$hash = password_hash($test_pass, PASSWORD_DEFAULT);
echo "\nTest Hashing:\n";
echo "Original: $test_pass\n";
echo "Hashed: $hash\n";
echo "Verify: " . (password_verify($test_pass, $hash) ? "YES" : "NO") . "\n";

// Check current student
if (isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];
    $sql = "SELECT student_id, email, password, LENGTH(password) as pass_len FROM Students WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        echo "\nCurrent Student:\n";
        echo "ID: " . $student['student_id'] . "\n";
        echo "Email: " . $student['email'] . "\n";
        echo "Password Length: " . $student['pass_len'] . "\n";
        
        // Test with default password
        echo "\nTest with 'password':\n";
        echo "Verify 'password': " . (password_verify('password', $student['password']) ? "YES" : "NO") . "\n";
        
        echo "\nPassword Hash Info:\n";
        $info = password_get_info($student['password']);
        print_r($info);
    } else {
        echo "\nStudent not found in database!\n";
    }
}

echo "</pre>";
?>