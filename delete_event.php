<?php
// ============================================================
// DELETE EVENT
// College Event Management System
// ============================================================
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Validate event ID
$event_id = intval($_GET['id'] ?? 0);
if ($event_id <= 0) {
    header('Location: events.php');
    exit();
}

// DELETE event (CASCADE will handle related registrations)
$stmt = mysqli_prepare($conn, "DELETE FROM events WHERE event_id = ?");
mysqli_stmt_bind_param($stmt, "i", $event_id);

if (mysqli_stmt_execute($stmt)) {
    header('Location: events.php?msg=deleted');
} else {
    header('Location: events.php?msg=delete_error');
}

mysqli_stmt_close($stmt);
exit();
?>
