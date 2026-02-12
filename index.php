<?php
$page_title = "Welcome to University of Vavuniya";
$page_icon = "fas fa-home";

include 'includes/header.php';
?>

<style>
/* University Banner */
.university-banner {
    background: linear-gradient(rgba(103, 0, 71, 0.9), rgba(103, 0, 71, 0.8)),
                url('images/university-bg.jpg') center/cover no-repeat;
    color: white;
    padding: 80px 0;
    margin-bottom: 40px;
    border-radius: 10px;
}

.banner-content {
    text-align: center;
}

/* UGC Logo Section */
.ugc-section {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    margin: 30px 0;
    text-align: center;
    border-top: 5px solid #FFD700;
}

.ugc-logo {
    max-height: 80px;
    margin: 20px auto;
    display: block;
}

/* University Info Cards */
.info-card {
    transition: transform 0.3s ease;
    border: none;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 20px;
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.info-card .card-header {
    background: #670047;
    color: white;
    padding: 15px;
    font-weight: 600;
}

.info-card .card-body {
    padding: 20px;
}

/* Quick Stats */
.quick-stats {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    margin: 30px 0;
}

.stat-item {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 10px;
    flex: 1;
    min-width: 150px;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #670047;
    display: block;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

/* Feature List */
.feature-list {
    list-style: none;
    padding: 0;
}

.feature-list li {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.feature-list li:last-child {
    border-bottom: none;
}

.feature-list i {
    color: #670047;
    margin-right: 10px;
}
</style>

<!-- University Banner -->
<div class="university-banner">
    <div class="container">
        <div class="banner-content">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fas fa-graduation-cap me-2"></i>
                University of Vavuniya
            </h1>
            <p class="lead mb-4">Sri Lanka's Premier Higher Education Institution in Northern Province</p>
            <div class="d-flex justify-content-center align-items-center">
                <div class="me-4">
                    <img src="images/logo.png" alt="UoV Logo" class="img-fluid" style="max-height: 80px;">
                </div>
                <div class="border-start ps-4">
                    <h4>Established in 2014</h4>
                    <p>Under the University Grants Commission, Sri Lanka</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- UGC Recognition -->
<div class="container">
    <div class="ugc-section">
        <h3 class="mb-4">
            <i class="fas fa-award text-warning me-2"></i>
            Recognized by University Grants Commission (UGC)
        </h3>
        <img src="images/ugc_logo.png" alt="UGC Logo" class="ugc-logo">
        <p class="mt-3">
            The University of Vavuniya is a public university established under the 
            Universities Act No. 16 of 1978 and is recognized by the University Grants Commission 
            of Sri Lanka.
        </p>
    </div>
</div>

<!-- University Information -->
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="info-card">
                <div class="card-header">
                    <i class="fas fa-university me-2"></i>About University of Vavuniya
                </div>
                <div class="card-body">
                    <p>The University of Vavuniya is a public university located in Vavuniya, 
                    Northern Province, Sri Lanka. It was established in 2014.</p>
                    
                    <h6><i class="fas fa-map-marker-alt text-primary me-2"></i>Campus Location</h6>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle text-success me-2"></i>Main Campus: Pambaimadhu, Vavuniya</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>City Campus: Vavuniya Town</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Area: 350 acres</li>
                    </ul>
                    
                    <h6><i class="fas fa-phone text-primary me-2"></i>Contact Information</h6>
                    <p>
                        <i class="fas fa-phone me-2"></i>+94 24 222 2265<br>
                        <i class="fas fa-fax me-2"></i>+94 24 222 2266<br>
                        <i class="fas fa-envelope me-2"></i>info@vau.ac.lk<br>
                        <i class="fas fa-globe me-2"></i>https://www.vau.ac.lk
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="info-card">
                <div class="card-header">
                    <i class="fas fa-graduation-cap me-2"></i>Faculties
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-flask text-info me-2"></i>Applied Sciences</h6>
                            <ul class="feature-list">
                                <li>B.Sc. in Physical Science</li>
                                <li>B.Sc. in Biological Science</li>
                                <li>B.Sc. in Mathematical Science</li>
                            </ul>
                            
                            <h6><i class="fas fa-laptop-code text-info me-2"></i>Technological Studies</h6>
                            <ul class="feature-list">
                                <li>B.Sc. in Information Systems</li>
                                <li>B.Sc. in Information Technology</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h6><i class="fas fa-chart-line text-info me-2"></i>Business Studies</h6>
                            <ul class="feature-list">
                                <li>B.B.A. in Business Administration</li>
                                <li>B.Sc. in Finance</li>
                                <li>B.Sc. in Marketing</li>
                            </ul>
                            
                            <h6><i class="fas fa-users text-info me-2"></i>Humanities & Social Sciences</h6>
                            <ul class="feature-list">
                                <li>B.A. in Sociology</li>
                                <li>B.A. in Languages</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="container">
    <div class="quick-stats">
        <div class="stat-item">
            <span class="stat-number">1,500+</span>
            <span class="stat-label">Students</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">100+</span>
            <span class="stat-label">Academic Staff</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">4</span>
            <span class="stat-label">Faculties</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">20+</span>
            <span class="stat-label">Degree Programs</span>
        </div>
    </div>
</div>

<!-- Course Registration System -->
<div class="container">
    <div class="row mt-4">
        <div class="col-md-4 mb-4">
            <div class="card h-100 info-card">
                <div class="card-body text-center">
                    <i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>
                    <h4>For Students</h4>
                    <p>Browse courses, enroll online, track your academic progress</p>
                    <a href="register.php" class="btn btn-primary">Register Now</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 info-card">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-3x text-success mb-3"></i>
                    <h4>Course Catalog</h4>
                    <p>View all available courses with details, schedules, and availability</p>
                    <a href="courses.php" class="btn btn-success">Browse Courses</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 info-card">
                <div class="card-body text-center">
                    <i class="fas fa-cogs fa-3x text-warning mb-3"></i>
                    <h4>Administration</h4>
                    <p>Manage students, courses, and enrollments efficiently</p>
                    <a href="login.php" class="btn btn-warning">Admin Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Features -->
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h4><i class="fas fa-star me-2"></i>System Features</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <p><i class="fas fa-check text-success me-2"></i>Easy Registration</p>
                </div>
                <div class="col-md-3">
                    <p><i class="fas fa-check text-success me-2"></i>Course Browsing</p>
                </div>
                <div class="col-md-3">
                    <p><i class="fas fa-check text-success me-2"></i>Online Enrollment</p>
                </div>
                <div class="col-md-3">
                    <p><i class="fas fa-check text-success me-2"></i>Progress Tracking</p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <p><i class="fas fa-check text-success me-2"></i>Secure Login</p>
                </div>
                <div class="col-md-3">
                    <p><i class="fas fa-check text-success me-2"></i>Real-time Updates</p>
                </div>
                <div class="col-md-3">
                    <p><i class="fas fa-check text-success me-2"></i>User Friendly</p>
                </div>
                <div class="col-md-3">
                    <p><i class="fas fa-check text-success me-2"></i>24/7 Access</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>