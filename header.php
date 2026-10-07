<?php
// ============================================================
// HEADER - Reusable Navigation
// College Event Management System
// ============================================================
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get current page for active nav highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Event Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar">
    <div class="navbar-brand">
        <span>🎓</span> College Event Manager
    </div>
    <ul class="navbar-nav">
        <li><a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">📊 Dashboard</a></li>
        <li><a href="students.php" class="<?php echo ($current_page == 'students.php' || $current_page == 'add_student.php' || $current_page == 'edit_student.php') ? 'active' : ''; ?>">👥 Students</a></li>
        <li><a href="events.php" class="<?php echo ($current_page == 'events.php' || $current_page == 'add_event.php' || $current_page == 'edit_event.php') ? 'active' : ''; ?>">📅 Events</a></li>
        <li><a href="registrations.php" class="<?php echo ($current_page == 'registrations.php') ? 'active' : ''; ?>">📋 Registrations</a></li>
        <li><a href="register_event.php" class="<?php echo ($current_page == 'register_event.php') ? 'active' : ''; ?>">➕ Register Student</a></li>
    </ul>
    <div class="navbar-user">
        <div class="user-icon"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
        <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
        <a href="logout.php" class="btn-logout">🚪 Logout</a>
    </div>
</nav>

<!-- Main Content Start -->
<div class="main-content">
