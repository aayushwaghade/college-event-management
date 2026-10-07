<?php
// ============================================================
// DATABASE INITIALIZATION / MIGRATION SCRIPT
// College Event Management System
// Safely creates tables and populates sample data on any MySQL/TiDB database.
// Can be run locally or on Vercel production.
// ============================================================

require_once 'db.php';

$messages = [];
$errors = [];

// 1. Create Admins Table
$sql_admins = "CREATE TABLE IF NOT EXISTS admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($conn, $sql_admins)) {
    $messages[] = "Table 'admins' verified/created.";
} else {
    $errors[] = "Error creating 'admins': " . mysqli_error($conn);
}

// 2. Create Students Table
$sql_students = "CREATE TABLE IF NOT EXISTS students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    student_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    department VARCHAR(100),
    year VARCHAR(20),
    roll_number VARCHAR(30) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($conn, $sql_students)) {
    $messages[] = "Table 'students' verified/created.";
} else {
    $errors[] = "Error creating 'students': " . mysqli_error($conn);
}

// 3. Create Events Table
$sql_events = "CREATE TABLE IF NOT EXISTS events (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($conn, $sql_events)) {
    $messages[] = "Table 'events' verified/created.";
} else {
    $errors[] = "Error creating 'events': " . mysqli_error($conn);
}

// 4. Create Registrations Table
$sql_registrations = "CREATE TABLE IF NOT EXISTS registrations (
    registration_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date DATE NOT NULL,
    status ENUM('Registered','Approved','Cancelled') DEFAULT 'Registered',
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (student_id, event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($conn, $sql_registrations)) {
    $messages[] = "Table 'registrations' verified/created.";
} else {
    $errors[] = "Error creating 'registrations': " . mysqli_error($conn);
}

// 5. Seed Admins if empty
$chk_admin = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM admins");
$row = mysqli_fetch_assoc($chk_admin);
if ($row['cnt'] == 0) {
    $seed_admin = "INSERT INTO admins (username, password, full_name) VALUES
        ('admin', 'admin123', 'System Administrator')";
    if (mysqli_query($conn, $seed_admin)) {
        $messages[] = "Default admin account created (admin / admin123).";
    }
} else {
    $messages[] = "Admin account already exists.";
}

// 6. Seed Students if empty
$chk_students = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM students");
$row = mysqli_fetch_assoc($chk_students);
if ($row['cnt'] == 0) {
    $seed_students = "INSERT INTO students (student_name, email, phone, department, year, roll_number) VALUES
        ('Aarav Sharma', 'aarav.sharma@college.edu', '9876543210', 'Computer Science & Engineering', 'Third Year', 'CSE2023001'),
        ('Priya Patel', 'priya.patel@college.edu', '9876543211', 'Information Technology', 'Second Year', 'IT2024001'),
        ('Rohan Gupta', 'rohan.gupta@college.edu', '9876543212', 'Mechanical Engineering', 'Fourth Year', 'ME2022001'),
        ('Sneha Reddy', 'sneha.reddy@college.edu', '9876543213', 'Electronics & Telecommunication', 'Third Year', 'ENTC2023001'),
        ('Vikram Singh', 'vikram.singh@college.edu', '9876543214', 'Computer Science & Engineering', 'Second Year', 'CSE2024001'),
        ('Ananya Joshi', 'ananya.joshi@college.edu', '9876543215', 'Civil Engineering', 'First Year', 'CE2025001'),
        ('Karthik Nair', 'karthik.nair@college.edu', '9876543216', 'Information Technology', 'Third Year', 'IT2023001'),
        ('Divya Menon', 'divya.menon@college.edu', '9876543217', 'Computer Science & Engineering', 'Fourth Year', 'CSE2022001'),
        ('Rahul Deshmukh', 'rahul.deshmukh@college.edu', '9876543218', 'Mechanical Engineering', 'Second Year', 'ME2024001'),
        ('Meera Kulkarni', 'meera.kulkarni@college.edu', '9876543219', 'Electronics & Telecommunication', 'First Year', 'ENTC2025001')";
    if (mysqli_query($conn, $seed_students)) {
        $messages[] = "10 Sample students inserted.";
    }
} else {
    $messages[] = "Students table already has data (" . $row['cnt'] . " records).";
}

// 7. Seed Events if empty
$chk_events = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM events");
$row = mysqli_fetch_assoc($chk_events);
if ($row['cnt'] == 0) {
    $seed_events = "INSERT INTO events (event_name, description, event_date, event_time, venue, organizer, capacity, status) VALUES
        ('Tech Fest 2026', 'Annual technology festival featuring competitions, workshops, and exhibitions showcasing student innovations.', '2026-11-15', '09:00:00', 'Main Auditorium', 'CSE Department', 200, 'Upcoming'),
        ('Coding Competition', 'Inter-college coding competition with multiple rounds including aptitude, debugging, and problem solving.', '2026-10-25', '10:00:00', 'Computer Lab 1', 'Coding Club', 50, 'Upcoming'),
        ('Web Development Workshop', 'Hands-on workshop covering HTML, CSS, JavaScript, and PHP for building dynamic web applications.', '2026-10-20', '14:00:00', 'Seminar Hall B', 'IT Department', 40, 'Upcoming'),
        ('AI & Cloud Seminar', 'Expert talk on Artificial Intelligence, Machine Learning, and Cloud Computing trends in industry.', '2026-10-10', '11:00:00', 'Conference Room', 'Training & Placement Cell', 100, 'Ongoing'),
        ('Sports Day', 'Annual sports day with track and field events, team sports, and individual competitions.', '2026-12-05', '07:00:00', 'College Ground', 'Sports Committee', 300, 'Upcoming'),
        ('Cultural Fest', 'Three-day cultural festival with music, dance, drama, art exhibitions, and food stalls.', '2026-12-20', '16:00:00', 'Open Air Theatre', 'Cultural Committee', 500, 'Upcoming'),
        ('Database Workshop', 'Workshop on relational database design, SQL queries, normalization, and practical MySQL usage.', '2026-09-15', '10:00:00', 'Computer Lab 2', 'CSE Department', 35, 'Completed'),
        ('Resume Building Session', 'Interactive session on crafting professional resumes and preparing for campus placements.', '2026-09-28', '13:00:00', 'Seminar Hall A', 'Training & Placement Cell', 80, 'Completed')";
    if (mysqli_query($conn, $seed_events)) {
        $messages[] = "8 Sample events inserted.";
    }
} else {
    $messages[] = "Events table already has data (" . $row['cnt'] . " records).";
}

// 8. Seed Registrations if empty
$chk_regs = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM registrations");
$row = mysqli_fetch_assoc($chk_regs);
if ($row['cnt'] == 0) {
    // Only insert sample registrations if students and events exist
    $s_cnt = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM students"))['c'];
    $e_cnt = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM events"))['c'];
    if ($s_cnt >= 10 && $e_cnt >= 8) {
        $seed_regs = "INSERT INTO registrations (student_id, event_id, registration_date, status) VALUES
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
            (10, 6, '2026-10-06', 'Approved')";
        if (mysqli_query($conn, $seed_regs)) {
            $messages[] = "15 Sample registrations inserted.";
        }
    }
} else {
    $messages[] = "Registrations table already has data (" . $row['cnt'] . " records).";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - College Event Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #F8FAFC; padding: 40px 20px;">
    <div style="max-width: 650px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 32px; border: 1px solid #E2E8F0;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="font-size: 40px; margin-bottom: 8px;">🚀</div>
            <h2 style="color: #0F172A; margin: 0;">Database Setup & Migration</h2>
            <p style="color: #64748B; margin-top: 6px;">College Event Management System</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <strong>Errors Encountered:</strong>
                <ul style="margin: 8px 0 0 20px;">
                    <?php foreach ($errors as $e): ?>
                        <li><?php echo htmlspecialchars($e); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">Setup Execution Log</div>
            <ul style="list-style: none; padding: 16px; margin: 0; line-height: 1.8;">
                <?php foreach ($messages as $msg): ?>
                    <li style="color: #166534; font-size: 0.95rem;">✅ <?php echo htmlspecialchars($msg); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="login.php" class="btn btn-primary" style="display: inline-block; padding: 12px 28px; text-decoration: none;">🔐 Proceed to Login</a>
        </div>
    </div>
</body>
</html>
