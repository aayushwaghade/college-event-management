<?php
// ============================================================
// DELETE STUDENT
// College Event Management System
// ============================================================
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Validate student ID
$student_id = intval($_GET['id'] ?? 0);
if ($student_id <= 0) {
    header('Location: students.php');
    exit();
}

// DELETE student (CASCADE will handle related registrations)
$stmt = mysqli_prepare($conn, "DELETE FROM students WHERE student_id = ?");
mysqli_stmt_bind_param($stmt, "i", $student_id);

if (mysqli_stmt_execute($stmt)) {
    header('Location: students.php?msg=deleted');
} else {
    header('Location: students.php?msg=delete_error');
}

mysqli_stmt_close($stmt);
exit();
?>
