<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University of Vavuniya - Course Registration System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" href="https://www.vau.ac.lk/wp-content/uploads/2023/05/cropped-UoV-Fevicon-32x32.png">
</head>
<body>
    <!-- University Header -->
    <div class="university-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <img src="images/logo.png" 
                         alt="University of Vavuniya" class="img-fluid logo">
                </div>
                <div class="col-md-8 text-center">
                    <h1 class="university-title">University of Vavuniya</h1>
                    <p class="university-subtitle">Course Registration & Enrollment Management System</p>
                </div>
                <div class="col-md-2 text-end">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i><?php echo $_SESSION['full_name']; ?>
                            </button>
                            <ul class="dropdown-menu">
                                <?php if($_SESSION['user_type'] == 'student'): ?>
                                    <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                    <li><a class="dropdown-item" href="courses.php"><i class="fas fa-book me-2"></i>Browse Courses</a></li>
                                    <li><a class="dropdown-item" href="enrollments.php"><i class="fas fa-clipboard-list me-2"></i>My Enrollments</a></li>
                                <?php elseif($_SESSION['user_type'] == 'admin'): ?>
                                    <li><a class="dropdown-item" href="dashboard_admin.php"><i class="fas fa-cogs me-2"></i>Dashboard</a></li>
                                    <li><a class="dropdown-item" href="students.php"><i class="fas fa-users me-2"></i>Manage Students</a></li>
                                <?php endif; ?>
                                
                                <!-- Change Password Link (ඇතුලත් කරන්න) -->
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="change_password.php"><i class="fas fa-key me-2"></i>Change Password</a></li>
                                <li><hr class="dropdown-divider"></li>
                                
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark main-navbar">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i>Home</a>
                    </li>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if($_SESSION['user_type'] == 'student'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="courses.php"><i class="fas fa-book me-1"></i>Browse Courses</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="enrollments.php"><i class="fas fa-clipboard-list me-1"></i>My Enrollments</a>
                            </li>
                        <?php elseif($_SESSION['user_type'] == 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="dashboard_admin.php"><i class="fas fa-cogs me-1"></i>Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="students.php"><i class="fas fa-users me-1"></i>Students</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <div class="d-flex">
                        <a href="login.php" class="btn btn-outline-light me-2">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                        <a href="register.php" class="btn btn-primary">
                            <i class="fas fa-user-plus me-1"></i>Register
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container main-container mt-4">