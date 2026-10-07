# COLLEGE EVENT MANAGEMENT SYSTEM
## DBMS Mini Project Documentation

---

## 1. Project Title

**College Event Management System**

---

## 2. Aim

To develop a web-based College Event Management System using PHP and MySQL that allows an administrator to manage students, college events, and event registrations efficiently using a relational database.

---

## 3. Objectives

1. To create a relational database for managing college event data
2. To implement CRUD (Create, Read, Update, Delete) operations on students, events, and registrations
3. To demonstrate DBMS concepts including tables, primary keys, foreign keys, joins, and constraints
4. To develop an admin interface for managing the entire system
5. To implement data validation and prevent data inconsistencies
6. To demonstrate the use of SQL queries such as SELECT, INSERT, UPDATE, DELETE, JOIN, COUNT, GROUP BY, WHERE, and ORDER BY

---

## 4. Software Required

| Software | Purpose |
|----------|---------|
| XAMPP | Local web server (Apache + MySQL) |
| Apache | Web server for PHP |
| MySQL | Relational database management system |
| phpMyAdmin | Database administration tool |
| VS Code | Code editor |
| Web Browser | Chrome / Firefox / Edge |

---

## 5. Theory

### Relational Database Management System (RDBMS)
A Relational Database Management System stores data in tables (relations). Each table consists of rows (records/tuples) and columns (fields/attributes). Tables can be related to each other using primary keys and foreign keys.

### Key DBMS Concepts Used

- **Primary Key**: A unique identifier for each record in a table. Example: `student_id` in the students table.
- **Foreign Key**: A field that references the primary key of another table, establishing a relationship. Example: `student_id` in registrations references `student_id` in students.
- **UNIQUE Constraint**: Ensures that all values in a column are different. Example: `roll_number` in students must be unique.
- **ENUM**: A data type that restricts a column to a set of predefined values. Example: `status` in events can only be 'Upcoming', 'Ongoing', 'Completed', or 'Cancelled'.
- **JOIN**: Combines rows from two or more tables based on related columns. Used extensively in registration queries.
- **Aggregate Functions**: Functions like COUNT() that perform calculations on sets of values. Used for dashboard statistics.

### PHP and MySQLi
PHP (Hypertext Preprocessor) is a server-side scripting language. MySQLi (MySQL Improved) is a PHP extension for interacting with MySQL databases. It supports prepared statements for secure database operations.

---

## 6. System Overview

The College Event Management System is a web application designed for college administrators to:

- Maintain a database of students with their personal and academic details
- Create and manage college events with details such as date, time, venue, and capacity
- Register students for events while preventing duplicate registrations
- Track registration statuses (Registered, Approved, Cancelled)
- View dashboard statistics derived from actual database queries
- Search and filter records across all modules

The system runs locally using XAMPP and is accessed through a web browser at `http://localhost/college_event_management/`.

---

## 7. Major Modules

### Module 1: Admin Login / Logout
- Session-based authentication
- Single admin user
- Redirects unauthenticated users to login page

### Module 2: Dashboard
- Displays real-time statistics using COUNT queries
- Shows upcoming events with registration counts using JOIN and GROUP BY

### Module 3: Student Management
- CRUD operations on student records
- Search by name, roll number, and department
- Duplicate roll number prevention

### Module 4: Event Management
- CRUD operations on event records
- Search by event name, venue, and status
- Registration count per event using JOIN

### Module 5: Event Registration
- Register students for events
- Duplicate registration prevention (UNIQUE constraint)
- Event capacity checking
- Cancelled event restriction

### Module 6: Registration Management
- View registrations using INNER JOIN across three tables
- Update registration status
- Filter by event, status, and student department

### Module 7: Search and Filter
- Student search: name, roll number, department
- Event search: event name, venue, status
- Registration filter: event, status, department

### Module 8: Database Connection
- MySQLi connection with error handling
- Session initialization
- UTF-8 character encoding

---

## 8. System Workflow

```
Admin Login
    │
    ▼
Dashboard (View Statistics)
    │
    ├─── Student Management
    │       ├── Add Student
    │       ├── Edit Student
    │       ├── Delete Student
    │       └── Search Students
    │
    ├─── Event Management
    │       ├── Add Event
    │       ├── Edit Event
    │       ├── Delete Event
    │       └── Search/Filter Events
    │
    ├─── Event Registration
    │       └── Register Student for Event
    │
    ├─── Registration Management
    │       ├── View All Registrations
    │       ├── Update Status
    │       └── Filter Registrations
    │
    └─── Logout
```

---

## 9. Project Structure

```
college_event_management/
│
├── index.php                 # Entry point (redirects)
├── login.php                 # Admin login page
├── logout.php                # Logout handler
├── dashboard.php             # Dashboard with statistics
│
├── students.php              # List all students
├── add_student.php           # Add new student
├── edit_student.php          # Edit student
├── delete_student.php        # Delete student
│
├── events.php                # List all events
├── add_event.php             # Add new event
├── edit_event.php            # Edit event
├── delete_event.php          # Delete event
│
├── register_event.php        # Register student for event
├── registrations.php         # View all registrations
├── update_registration.php   # Update registration status
│
├── db.php                    # Database connection
├── header.php                # Reusable header/navigation
├── footer.php                # Reusable footer
├── style.css                 # Stylesheet
├── script.js                 # JavaScript
│
├── database.sql              # Database schema + sample data
├── README.txt                # Installation guide
├── PROJECT_DOCUMENTATION.md  # This file
│
└── assets/
    └── images/               # Image assets
```

---

## 10. Database Design

### Database Name: `college_event_management`

### Entity-Relationship Diagram

```
┌──────────────┐         ┌──────────────────┐         ┌──────────────┐
│   STUDENTS   │         │  REGISTRATIONS   │         │    EVENTS    │
├──────────────┤         ├──────────────────┤         ├──────────────┤
│ student_id   │◄───┐    │ registration_id  │    ┌───►│ event_id     │
│ student_name │    │    │ student_id (FK)  │────┘    │ event_name   │
│ email        │    └────│ event_id (FK)    │         │ description  │
│ phone        │         │ registration_date│         │ event_date   │
│ department   │         │ status           │         │ event_time   │
│ year         │         └──────────────────┘         │ venue        │
│ roll_number  │                                      │ organizer    │
│ created_at   │          1:N            N:1          │ capacity     │
└──────────────┘    Students ←→ Registrations ←→ Events│ status      │
                                                      │ created_at   │
                                                      └──────────────┘
```

**Relationship**: Many-to-Many between Students and Events, resolved through the Registrations junction table.

---

## 11. Table Descriptions

### Table 1: admins

| Column | Type | Constraints |
|--------|------|-------------|
| admin_id | INT | PRIMARY KEY, AUTO_INCREMENT |
| username | VARCHAR(50) | UNIQUE, NOT NULL |
| password | VARCHAR(255) | NOT NULL |
| full_name | VARCHAR(100) | — |

### Table 2: students

| Column | Type | Constraints |
|--------|------|-------------|
| student_id | INT | PRIMARY KEY, AUTO_INCREMENT |
| student_name | VARCHAR(100) | NOT NULL |
| email | VARCHAR(100) | NOT NULL |
| phone | VARCHAR(15) | — |
| department | VARCHAR(100) | — |
| year | VARCHAR(20) | — |
| roll_number | VARCHAR(30) | UNIQUE, NOT NULL |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |

### Table 3: events

| Column | Type | Constraints |
|--------|------|-------------|
| event_id | INT | PRIMARY KEY, AUTO_INCREMENT |
| event_name | VARCHAR(150) | NOT NULL |
| description | TEXT | — |
| event_date | DATE | NOT NULL |
| event_time | TIME | NOT NULL |
| venue | VARCHAR(150) | NOT NULL |
| organizer | VARCHAR(100) | — |
| capacity | INT | NOT NULL |
| status | ENUM('Upcoming','Ongoing','Completed','Cancelled') | DEFAULT 'Upcoming' |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |

### Table 4: registrations

| Column | Type | Constraints |
|--------|------|-------------|
| registration_id | INT | PRIMARY KEY, AUTO_INCREMENT |
| student_id | INT | NOT NULL, FOREIGN KEY → students(student_id) |
| event_id | INT | NOT NULL, FOREIGN KEY → events(event_id) |
| registration_date | DATE | NOT NULL |
| status | ENUM('Registered','Approved','Cancelled') | DEFAULT 'Registered' |

**Additional Constraints**: UNIQUE(student_id, event_id) — prevents duplicate registrations.

---

## 12. Database Relationships

| Relationship | Type | Description |
|-------------|------|-------------|
| Students → Registrations | 1:N (One-to-Many) | One student can have many registrations |
| Events → Registrations | 1:N (One-to-Many) | One event can have many registrations |
| Students ↔ Events | M:N (Many-to-Many) | Resolved through the registrations table |

**ON DELETE CASCADE**: When a student or event is deleted, their related registrations are automatically deleted.

---

## 13. Important SQL Queries

### Database and Table Creation
```sql
CREATE DATABASE college_event_management;

CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    student_name VARCHAR(100) NOT NULL,
    roll_number VARCHAR(30) UNIQUE NOT NULL
    -- ... other fields
) ENGINE=InnoDB;
```

### INSERT — Adding a Student
```sql
INSERT INTO students (student_name, email, phone, department, year, roll_number)
VALUES ('Aarav Sharma', 'aarav@college.edu', '9876543210', 'Computer Science & Engineering', 'Third Year', 'CSE2023001');
```

### SELECT — Fetching All Events
```sql
SELECT * FROM events ORDER BY event_date DESC;
```

### UPDATE — Updating Registration Status
```sql
UPDATE registrations SET status = 'Approved' WHERE registration_id = 1;
```

### DELETE — Deleting a Student
```sql
DELETE FROM students WHERE student_id = 5;
```

### INNER JOIN — Fetching Registrations with Student and Event Details
```sql
SELECT r.registration_id, s.student_name, s.roll_number,
       e.event_name, e.event_date, r.status
FROM registrations r
INNER JOIN students s ON r.student_id = s.student_id
INNER JOIN events e ON r.event_id = e.event_id
ORDER BY r.registration_id DESC;
```

### COUNT + GROUP BY — Registration Count per Event
```sql
SELECT e.event_name, COUNT(r.registration_id) AS reg_count
FROM events e
LEFT JOIN registrations r ON e.event_id = r.event_id
GROUP BY e.event_id;
```

### WHERE + LIKE — Searching Students
```sql
SELECT * FROM students
WHERE student_name LIKE '%Sharma%'
   OR roll_number LIKE '%CSE%';
```

### COUNT + WHERE — Dashboard Statistics
```sql
SELECT COUNT(*) AS total FROM students;
SELECT COUNT(*) AS total FROM events WHERE status = 'Upcoming';
SELECT COUNT(*) AS total FROM registrations WHERE status = 'Approved';
```

---

## 14. Module Explanations

### Login Module
The admin enters their username and password. The system queries the `admins` table to verify credentials. On success, PHP session variables are set and the admin is redirected to the dashboard. All protected pages check for session validity.

### Dashboard Module
The dashboard executes multiple COUNT queries to calculate total students, events, registrations, and their subcategories. Upcoming events are fetched using a LEFT JOIN with registrations to include the registration count per event, grouped using GROUP BY.

### Student Module
Provides full CRUD functionality. The add form validates required fields and checks for duplicate roll numbers. The edit form pre-populates existing data. Search uses LIKE with WHERE conditions on multiple columns. Prepared statements prevent SQL injection.

### Event Module
Similar CRUD functionality as students. Events include a status field using ENUM. The events listing uses LEFT JOIN to show the registration count alongside each event. Search supports filtering by name, venue, and status.

### Registration Module
The registration form allows selecting a student and event from dropdown lists populated from the database. Before inserting, the system checks for: duplicate registration, cancelled events, and full capacity. The registrations listing uses INNER JOIN across all three tables.

### Registration Management
Displays registrations with complete student and event details using JOIN queries. Admins can update registration status through inline forms. Filters allow narrowing results by event, status, and department.

---

## 15. Testing and Verification

| Test Case | Action | Expected Result |
|-----------|--------|----------------|
| TC-01 | Open project URL | Login page displayed |
| TC-02 | Login with valid credentials | Dashboard displayed |
| TC-03 | Login with invalid credentials | Error message shown |
| TC-04 | Add a new student | Student added successfully |
| TC-05 | Edit a student | Student updated successfully |
| TC-06 | Delete a student | Student deleted successfully |
| TC-07 | Add a new event | Event added successfully |
| TC-08 | Edit an event | Event updated successfully |
| TC-09 | Delete an event | Event deleted successfully |
| TC-10 | Register student for event | Registration created |
| TC-11 | Duplicate registration attempt | Error message shown |
| TC-12 | Update registration status | Status updated |
| TC-13 | Search students | Filtered results displayed |
| TC-14 | Filter registrations | Filtered results displayed |
| TC-15 | Logout | Redirected to login page |

---

## 16. Advantages

1. Eliminates manual record-keeping for events and registrations
2. Provides centralized management of students and events
3. Prevents data duplication through database constraints
4. Real-time statistics on the dashboard
5. Search and filter capabilities for quick data retrieval
6. Maintains data integrity through foreign keys
7. Simple and intuitive user interface
8. Easy to install and run locally using XAMPP

---

## 17. Limitations

1. Single admin user (no multi-user support)
2. No email notifications for registrations
3. No student self-registration portal
4. Designed for local/offline use only
5. Simple password authentication (not hashed)
6. No data export functionality (CSV/PDF)
7. No audit trail or activity logging

---

## 18. Future Scope

1. Add student self-registration portal with individual login
2. Implement password hashing for improved security
3. Add email notifications for registration confirmations
4. Implement data export to CSV and PDF formats
5. Add event categories and tagging system
6. Implement attendance tracking for events
7. Add event feedback and rating system
8. Deploy on a live web server for online access
9. Add role-based access control for multiple admins
10. Implement report generation with charts and graphs

---

## 19. Conclusion

The College Event Management System successfully demonstrates the practical application of DBMS concepts in a real-world scenario. The project implements a fully functional relational database with proper table design, primary keys, foreign keys, and constraints. All major SQL operations (CREATE, INSERT, SELECT, UPDATE, DELETE, JOIN, COUNT, GROUP BY, WHERE, ORDER BY) are used in the actual application logic, not just in documentation. The system provides a clean, professional interface for managing college events and student registrations efficiently.

---

*Project developed as a DBMS Mini Project for academic purposes.*
