    </div> <!-- Close main-container -->

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-university me-2"></i>University of Vavuniya</h5>
                    <p class="footer-text">
                        Pambaimadhu, Vavuniya, Sri Lanka<br>
                        Phone: +94 24 222 2265<br>
                        Email: info@vau.ac.lk
                    </p>
                </div>
                <div class="col-md-4">
                    <h5><i class="fas fa-link me-2"></i>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php"><i class="fas fa-chevron-right me-1"></i>Home</a></li>
                        <li><a href="courses.php"><i class="fas fa-chevron-right me-1"></i>Courses</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5><i class="fas fa-info-circle me-2"></i>System Information</h5>
                    <p class="footer-text">
                        Course Registration System<br>
                        Version 1.0<br>
                        © <?php echo date('Y'); ?> University of Vavuniya
                    </p>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="copyright">
                        &copy; <?php echo date('Y'); ?> University of Vavuniya. All Rights Reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="js/script.js"></script>
    
    <?php if(isset($custom_js)): ?>
        <script><?php echo $custom_js; ?></script>
    <?php endif; ?>
</body>
</html>