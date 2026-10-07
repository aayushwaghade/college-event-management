<?php
// ============================================================
// UPDATE REGISTRATION STATUS
// College Event Management System
// ============================================================
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle POST request only
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_id = intval($_POST['registration_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');

    // Validate inputs
    $valid_statuses = ['Registered', 'Approved', 'Cancelled'];
    if ($registration_id <= 0 || !in_array($status, $valid_statuses)) {
        header('Location: registrations.php?msg=error');
        exit();
    }

    // UPDATE registration status
    $stmt = mysqli_prepare($conn, "UPDATE registrations SET status = ? WHERE registration_id = ?");
    mysqli_stmt_bind_param($stmt, "si", $status, $registration_id);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: registrations.php?msg=updated');
    } else {
        header('Location: registrations.php?msg=error');
    }

    mysqli_stmt_close($stmt);
} else {
    header('Location: registrations.php');
}
exit();
?>
