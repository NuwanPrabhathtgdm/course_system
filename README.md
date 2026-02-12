# course_system
This system replaces the traditional manual course registration process with a modern, efficient web application. Students can register online, browse available courses, enroll with real-time seat availability, and track their academic progress. Administrators gain full control over student management and can generate enrollment reports.


This system replaces the traditional manual course registration process with a modern, efficient web application. Students can register online, browse available courses, enroll with real-time seat availability, and track their academic progress. Administrators gain full control over student management and can generate enrollment reports.

Problem Statement:

Manual registration causes long queues and paperwork

No real-time visibility of course availability

Difficulty in managing student records

Time-consuming administrative tasks

Solution:
A centralized platform that handles student registration, course enrollment, and administrative management with real-time updates and secure authentication.

🛠 Built With
Frontend:

HTML5, CSS3, JavaScript (ES6+)

Bootstrap 5 - Responsive UI framework

Font Awesome 6 - Icons

Google Fonts (Roboto)

Backend:

PHP 8.x - Server-side scripting

MySQL 8.x - Relational database

Apache - Web server

Development Tools:

XAMPP / WAMP

Visual Studio Code

Git & GitHub

phpMyAdmin

✨ Features
Student Portal
Feature	Description
🔐 Secure Authentication	Login with email/password, password hashing with bcrypt
📝 Student Registration	Auto-generates registration number (UV/FAC/YYYY/001)
📚 Course Catalog	Browse all open courses with search and filter by faculty, semester
📊 Real-time Availability	Visual progress bars showing seat occupancy
✅ One-Click Enrollment	Enroll in courses instantly with capacity validation
📋 My Enrollments	View registered courses with status and dates
🗑️ Drop Course	Remove enrollment within allowed period
🔑 Password Change	Secure password update with strength meter
📝 Feedback	Submit course feedback
🔔 Notifications	View system announcements
Admin Panel
Feature	Description
👥 Student Management	CRUD operations, activate/deactivate, delete
📈 Dashboard	Key metrics: total students, courses, enrollments
📊 Reports	Course-wise enrollment statistics
🔍 Search & Filter	Advanced filtering by faculty, status, year
🛡️ Role-based Access	Separate admin authentication
System Features
✅ Responsive Design - Works on desktop, tablet, and mobile

✅ University Branding - Official colors (#670047, #FFD700)

✅ Form Validation - Client & server-side validation

✅ SQL Injection Prevention - Prepared statements

✅ Session Management - Secure session handling with regeneration

✅ Password Security - Bcrypt hashing (60+ character hashes)

✅ AJAX Integration - Seamless enrollment without page reload

✅ Transaction Support - ACID compliance for enrollments

✅ Waitlist - Students auto-added when course is full

✅ CSRF Protection - Token-based form security

🏗 System Architecture
text
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│                 │     │                 │     │                 │
│   Browser       │────▶│   Apache        │────▶│   PHP           │
│   (Frontend)    │◀────│   (Server)      │◀────│   (Logic)       │
│                 │     │                 │     │                 │
└─────────────────┘     └─────────────────┘     └────────┬────────┘
                                                          │
                                                          ▼
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│                 │     │                 │     │                 │
│   MySQL         │◀────│   PHP           │     │   Bootstrap     │
│   (Database)    │────▶│   (PDO/mysqli)  │     │   (UI)          │
│                 │     │                 │     │                 │
└─────────────────┘     └─────────────────┘     └─────────────────┘
💾 Database Design
Tables:

Students - Student profiles, authentication

Courses - Course catalog, capacity, lecturer

Enrollments - Junction table with status tracking

Admin_Users - Administrator credentials

Notifications - Student notifications

Waitlist - Course waitlist management

Feedback - Student course feedback

ER Diagram Highlights:

One-to-many: Student → Enrollments

One-to-many: Course → Enrollments

Composite unique key on (student_id, course_id, academic_year)

Cascading deletes for referential integrity

🚀 Installation
Prerequisites
PHP 7.4 or higher

MySQL 5.7 or higher

Apache/Nginx server

Web browser (Chrome, Firefox, Edge)

Step-by-Step Setup
Clone the repository

bash
git clone https://github.com/yourusername/university-course-registration.git
cd university-course-registration
Move to server directory

XAMPP: C:\xampp\htdocs\course_system\

WAMP: C:\wamp64\www\course_system\

LAMP: /var/www/html/course_system/

Import Database

Open phpMyAdmin

Create database: uv_registration

Import database/course_registration.sql

Configure Database Connection
Edit includes/db.php:

php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'uv_registration');
Run the Application

Navigate to: http://localhost/course_system/

Default Login Credentials
Admin:

Email: admin@vau.ac.lk

Password: password123

Student:

Email: test@vau.ac.lk

Password: password123

📱 Usage Guide
For Students:
Register - Create new account (auto-generates reg number)

Login - Access dashboard

Browse Courses - Search/filter available courses

Enroll - Click "Enroll" on any open course

Track - View enrolled courses in dashboard

Drop - Remove enrollment if needed

For Administrators:
Login - Use admin credentials

Dashboard - View system statistics

Manage Students - View, edit, activate/deactivate

Reports - View enrollment analytics

Course Management - (Planned) Add/edit courses

📸 Screenshots
[Add actual screenshots here after running the project]

Home Page - University banner with system introduction

Student Dashboard - Overview of enrolled courses

Course Catalog - Available courses with progress bars

Admin Dashboard - Statistics and quick actions

Student Management - CRUD interface for admin

🚧 Future Enhancements
Email Notifications - SMTP integration for enrollment confirmations

Grade Management - Admin grade entry, student GPA calculation

Payment Integration - Course fee payments

QR Code Attendance - Generate QR for each enrollment

Mobile App - React Native / Flutter version

Export Reports - PDF/Excel download

Multi-language Support - Sinhala/Tamil language toggle

Two-Factor Authentication - Enhanced security

API Development - RESTful API for mobile integration

Course Prerequisites - Auto-check before enrollment

🤝 Contributing
Contributions are welcome! Please follow these steps:

Fork the repository

Create your feature branch (git checkout -b feature/AmazingFeature)

Commit changes (git commit -m 'Add AmazingFeature')

Push to branch (git push origin feature/AmazingFeature)

Open a Pull Request

📄 License
This project is licensed under the MIT License - see the LICENSE file for details.

📬 Contact
Developer: T.G.D.M.N.Prabhath

LinkedIn: www.linkedin.com/in/nuwan-prabhath-619597373

Email: nuwanprabhathtgdm@gmail.com

University: University of Vavuniya, Sri Lanka
Course: 3rd Year 1st Semester - Advanced Web Technology Project
Academic Year: 2024/2025

<p align="center"> Developed with ❤️ for the University of Vavuniya Academic Community </p>
📊 Project Status: ✅ Complete (Version 1.0)
This project successfully demonstrates a fully functional course registration system with all core features implemented. It serves as an excellent foundation for future university information system developments.
