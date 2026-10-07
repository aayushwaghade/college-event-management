============================================================
 COLLEGE EVENT MANAGEMENT SYSTEM
 README - Installation and Testing Guide
============================================================

PROJECT NAME:
College Event Management System

OBJECTIVE:
A web-based College Event Management System that allows
an administrator to manage students, college events, and
event registrations using a MySQL relational database.

TECHNOLOGY STACK:
- Frontend: HTML5, CSS3, Vanilla JavaScript
- Backend: PHP
- Database: MySQL
- Server: XAMPP (Apache + MySQL)
- Database Tool: phpMyAdmin

FEATURES:
1. Admin Login / Logout (Session-based)
2. Dashboard with live statistics
3. Student Management (Add, Edit, Delete, Search)
4. Event Management (Add, Edit, Delete, Search)
5. Event Registration with duplicate prevention
6. Registration Management with status updates
7. Search and Filter functionality
8. Relational database with foreign keys

DATABASE:
- Database Name: college_event_management
- Tables: admins, students, events, registrations

============================================================
 INSTALLATION STEPS
============================================================

Step 1: Install XAMPP
- Download and install XAMPP from https://www.apachefriends.org/
- Install in default location: C:\xampp

Step 2: Start Services
- Open XAMPP Control Panel
- Start Apache
- Start MySQL

Step 3: Copy Project Files
- Copy the entire "college_event_management" folder to:
  C:\xampp\htdocs\college_event_management

Step 4: Create Database
- Open browser and go to: http://localhost/phpmyadmin
- Click "Import" tab
- Select the file: database.sql (from the project folder)
- Click "Go" to import
- The database "college_event_management" will be created
  with all tables and sample data

Step 5: Open Project
- Open browser and go to:
  http://localhost/college_event_management/

Step 6: Login
- Username: admin
- Password: admin123

============================================================
 ADMIN LOGIN CREDENTIALS
============================================================

Username: admin
Password: admin123

============================================================
 PROJECT URL
============================================================

http://localhost/college_event_management/

============================================================
 MODULES
============================================================

1. Login / Logout
   - Session-based authentication
   - Error messages for invalid credentials

2. Dashboard
   - Total Students, Events, Registrations
   - Upcoming Events count
   - Approved/Cancelled Registration counts
   - Upcoming Events table with registration count

3. Student Management
   - View all students in table format
   - Add new student with validation
   - Edit student information
   - Delete student (handles relationships)
   - Search by name, roll number, department

4. Event Management
   - View all events with registration count
   - Add new event with all details
   - Edit event information
   - Delete event (handles relationships)
   - Filter by status and search

5. Event Registration
   - Register student for event
   - Prevent duplicate registration
   - Check event capacity
   - Prevent registration for cancelled events
   - Auto-set registration date

6. Registration Management
   - View all registrations using JOIN queries
   - Update registration status
   - Filter by event, status, department

============================================================
 DATABASE TABLES
============================================================

1. admins
   - admin_id (PK), username, password, full_name

2. students
   - student_id (PK), student_name, email, phone,
     department, year, roll_number (UNIQUE), created_at

3. events
   - event_id (PK), event_name, description, event_date,
     event_time, venue, organizer, capacity, status, created_at

4. registrations
   - registration_id (PK), student_id (FK), event_id (FK),
     registration_date, status
   - UNIQUE constraint on (student_id, event_id)

============================================================
 TEST CASES
============================================================

TC-01: Open Project
  Action: Navigate to http://localhost/college_event_management/
  Expected: Login page is displayed

TC-02: Admin Login
  Action: Enter username "admin" and password "admin123", click Login
  Expected: Redirected to Dashboard with statistics

TC-03: Invalid Login
  Action: Enter wrong username/password, click Login
  Expected: Error message "Invalid login credentials."

TC-04: Add Student
  Action: Go to Students > Add New Student, fill form, submit
  Expected: Student added, redirected to students list with success message

TC-05: Edit Student
  Action: Click Edit on a student, modify fields, submit
  Expected: Student updated with success message

TC-06: Delete Student
  Action: Click Delete on a student, confirm
  Expected: Student deleted with success message

TC-07: Add Event
  Action: Go to Events > Add New Event, fill form, submit
  Expected: Event added, redirected to events list with success message

TC-08: Edit Event
  Action: Click Edit on an event, modify fields, submit
  Expected: Event updated with success message

TC-09: Delete Event
  Action: Click Delete on an event, confirm
  Expected: Event deleted with success message

TC-10: Register Student for Event
  Action: Go to Register Student, select student and event, submit
  Expected: Registration created with success message

TC-11: Prevent Duplicate Registration
  Action: Try to register same student for same event again
  Expected: Error "Student is already registered for this event."

TC-12: Update Registration Status
  Action: Go to Registrations, change status dropdown, click Update
  Expected: Status updated with success message

TC-13: Search Student
  Action: Enter name or roll number in search box, click Search
  Expected: Filtered student list displayed

TC-14: Filter Registrations
  Action: Select event/status/department filter, click Filter
  Expected: Filtered registrations displayed

TC-15: Logout
  Action: Click Logout button
  Expected: Session destroyed, redirected to login page

============================================================
 TROUBLESHOOTING
============================================================

Problem: Page shows database connection error
Solution: 
- Make sure XAMPP Apache and MySQL are running
- Make sure database "college_event_management" exists
- Import database.sql through phpMyAdmin

Problem: Login not working
Solution:
- Make sure database is imported
- Use credentials: admin / admin123

Problem: CSS not loading
Solution:
- Make sure style.css is in the project root folder
- Clear browser cache (Ctrl + Shift + R)

Problem: Page not found (404)
Solution:
- Make sure project folder is at:
  C:\xampp\htdocs\college_event_management\
- Check URL: http://localhost/college_event_management/

============================================================
 DBMS CONCEPTS DEMONSTRATED
============================================================

- CREATE DATABASE
- CREATE TABLE
- PRIMARY KEY
- FOREIGN KEY
- UNIQUE constraint
- INSERT INTO
- SELECT
- UPDATE
- DELETE
- INNER JOIN
- LEFT JOIN
- COUNT()
- GROUP BY
- WHERE
- ORDER BY
- LIKE (for search)
- Prepared Statements
- Session Management

============================================================
 END OF README
============================================================
