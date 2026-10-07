-- ============================================================
-- COLLEGE EVENT MANAGEMENT SYSTEM
-- Database: college_event_management
-- DBMS Mini Project
-- ============================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS college_event_management;

-- Select Database
USE college_event_management;

-- ============================================================
-- TABLE 1: admins
-- ============================================================
CREATE TABLE admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE 2: students
-- ============================================================
CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    student_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    department VARCHAR(100),
    year VARCHAR(20),
    roll_number VARCHAR(30) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABLE 3: events
-- ============================================================
CREATE TABLE events (
    event_id INT PRIMARY KEY AUTO_INCREMENT,
    event_name VARCHAR(150) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    venue VARCHAR(150) NOT NULL,
    organizer VARCHAR(100),
    capacity INT NOT NULL,
    status ENUM('Upcoming','Ongoing','Completed','Cancelled') DEFAULT 'Upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABLE 4: registrations
-- Relationship table between students and events
-- ============================================================
CREATE TABLE registrations (
    registration_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date DATE NOT NULL,
    status ENUM('Registered','Approved','Cancelled') DEFAULT 'Registered',
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (student_id, event_id)
) ENGINE=InnoDB;

-- ============================================================
-- INSERT SAMPLE DATA
-- ============================================================

-- Default Admin
INSERT INTO admins (username, password, full_name) VALUES
('admin', 'admin123', 'System Administrator');

-- Sample Students (8+)
INSERT INTO students (student_name, email, phone, department, year, roll_number) VALUES
('Aarav Sharma', 'aarav.sharma@college.edu', '9876543210', 'Computer Science & Engineering', 'Third Year', 'CSE2023001'),
('Priya Patel', 'priya.patel@college.edu', '9876543211', 'Information Technology', 'Second Year', 'IT2024001'),
('Rohan Gupta', 'rohan.gupta@college.edu', '9876543212', 'Mechanical Engineering', 'Fourth Year', 'ME2022001'),
('Sneha Reddy', 'sneha.reddy@college.edu', '9876543213', 'Electronics & Telecommunication', 'Third Year', 'ENTC2023001'),
('Vikram Singh', 'vikram.singh@college.edu', '9876543214', 'Computer Science & Engineering', 'Second Year', 'CSE2024001'),
('Ananya Joshi', 'ananya.joshi@college.edu', '9876543215', 'Civil Engineering', 'First Year', 'CE2025001'),
('Karthik Nair', 'karthik.nair@college.edu', '9876543216', 'Information Technology', 'Third Year', 'IT2023001'),
('Divya Menon', 'divya.menon@college.edu', '9876543217', 'Computer Science & Engineering', 'Fourth Year', 'CSE2022001'),
('Rahul Deshmukh', 'rahul.deshmukh@college.edu', '9876543218', 'Mechanical Engineering', 'Second Year', 'ME2024001'),
('Meera Kulkarni', 'meera.kulkarni@college.edu', '9876543219', 'Electronics & Telecommunication', 'First Year', 'ENTC2025001');

-- Sample Events (6+)
INSERT INTO events (event_name, description, event_date, event_time, venue, organizer, capacity, status) VALUES
('Tech Fest 2026', 'Annual technology festival featuring competitions, workshops, and exhibitions showcasing student innovations.', '2026-11-15', '09:00:00', 'Main Auditorium', 'CSE Department', 200, 'Upcoming'),
('Coding Competition', 'Inter-college coding competition with multiple rounds including aptitude, debugging, and problem solving.', '2026-10-25', '10:00:00', 'Computer Lab 1', 'Coding Club', 50, 'Upcoming'),
('Web Development Workshop', 'Hands-on workshop covering HTML, CSS, JavaScript, and PHP for building dynamic web applications.', '2026-10-20', '14:00:00', 'Seminar Hall B', 'IT Department', 40, 'Upcoming'),
('AI & Cloud Seminar', 'Expert talk on Artificial Intelligence, Machine Learning, and Cloud Computing trends in industry.', '2026-10-10', '11:00:00', 'Conference Room', 'Training & Placement Cell', 100, 'Ongoing'),
('Sports Day', 'Annual sports day with track and field events, team sports, and individual competitions.', '2026-12-05', '07:00:00', 'College Ground', 'Sports Committee', 300, 'Upcoming'),
('Cultural Fest', 'Three-day cultural festival with music, dance, drama, art exhibitions, and food stalls.', '2026-12-20', '16:00:00', 'Open Air Theatre', 'Cultural Committee', 500, 'Upcoming'),
('Database Workshop', 'Workshop on relational database design, SQL queries, normalization, and practical MySQL usage.', '2026-09-15', '10:00:00', 'Computer Lab 2', 'CSE Department', 35, 'Completed'),
('Resume Building Session', 'Interactive session on crafting professional resumes and preparing for campus placements.', '2026-09-28', '13:00:00', 'Seminar Hall A', 'Training & Placement Cell', 80, 'Completed');

-- Sample Registrations (10+)
INSERT INTO registrations (student_id, event_id, registration_date, status) VALUES
(1, 1, '2026-10-01', 'Approved'),
(1, 2, '2026-10-02', 'Registered'),
(2, 1, '2026-10-01', 'Approved'),
(2, 3, '2026-10-03', 'Registered'),
(3, 5, '2026-10-04', 'Approved'),
(4, 4, '2026-10-05', 'Registered'),
(5, 1, '2026-10-02', 'Approved'),
(5, 2, '2026-10-02', 'Registered'),
(6, 6, '2026-10-06', 'Registered'),
(7, 3, '2026-10-03', 'Approved'),
(7, 4, '2026-10-05', 'Cancelled'),
(8, 7, '2026-09-10', 'Approved'),
(8, 1, '2026-10-01', 'Registered'),
(9, 5, '2026-10-04', 'Registered'),
(10, 6, '2026-10-06', 'Approved');

-- ============================================================
-- END OF DATABASE SCRIPT
-- ============================================================
