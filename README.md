# tinkherhack
# Duty Leave and Attendance Management System

A role-based web application developed to digitize and manage the duty leave and attendance approval workflow in a college environment.

## Description

The Duty Leave and Attendance Management System is a PHP and MySQL based web application designed to automate and streamline the approval process for class representatives and club executive members. The system replaces manual paperwork with a structured multi-level approval workflow involving staff coordinators, the Dean, and an administrator.

Students who hold approved roles can submit duty leave or attendance requests along with mandatory supporting documents. Requests are forwarded to the designated staff coordinator, then escalated to the Dean for final approval. Once approved, all staff members can view the finalized records. The administrator verifies student registrations to prevent unauthorized access or misuse of the system.

This project demonstrates role-based authentication, multi-level approval logic, session management, secure password hashing, and database relationship handling in a real-world institutional use case.

## Getting Started

### Dependencies

* Windows 10 or later (or any OS capable of running Apache and MySQL)
* XAMPP (Apache and MySQL)
* PHP 8.x recommended
* MySQL 5.7 or later
* Web browser (Chrome, Edge, Firefox)

### Installing

* Download or clone the repository into your XAMPP `htdocs` folder
C:\xampp\htdocs\tinkherhack
* Start Apache and MySQL from XAMPP Control Panel
* Open phpMyAdmin
* Create a new database (for example: duty_leave_db)
* Import the provided SQL file or manually create required tables
* Configure database connection in:

Update database credentials if required.

### Executing program

* Start Apache and MySQL in XAMPP
* Open browser
* Navigate to:
http://localhost/tinkherhack/
* Log in using sample admin, staff, or dean credentials
* Register a new student and approve via admin panel
* Submit and process leave requests through workflow

## Help

If login redirects show "Unauthorized", verify:

* User role is correctly stored in the database
* User status is set to "approved"
* Session variables are being set correctly

If database connection fails, check:
config/db.php

Ensure database name, username, and password are correct.

## Authors

Meera Suresh  
Computer Science Engineering Student  

## Version History

* 1.0
    * Multi-level approval workflow implemented
    * Role-based authentication system
    * Admin verification system
    * File upload validation
    * Search functionality for staff
* 0.1
    * Initial system architecture and database design

## License

This project is licensed under the MIT License. See the LICENSE.md file for details.

## Acknowledgments

This project was developed as a practical academic workflow automation system and as a demonstration of backend system design and role-based application architecture.


